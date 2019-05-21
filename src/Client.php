<?php

// https://any.run/api-documentation/

namespace ANYRUN;

require_once 'WebClient.php';

class Client
{
    private $base_url = 'https://api.any.run/v1';
    private $authorization = null;

    public function __construct(...$auth_element)
    {
        if (count($auth_element) === 1) {
            $this->authorization = 'API-Key ' . $auth_element[0];
        } else if (count($auth_element) === 2) {
            $this->authorization = 'Basic ' . base64_encode($auth_element[0] . ':' . $auth_element[1]);
        }

        if ($this->authorization !== null) {
            new \Exception('Invalid config: set API key or password');
        }
    }

    /**
     * https://any.run/api-documentation/#api-Analysis-GetHistory
     *
     * @access public
     *
     * @param bool $is_team
     * @param int $skip
     * @param int $limit
     *
     * @return array $history
     */
    public function get_history(bool $is_team = false, int $skip = 0, int $limit = 25): array
    {
        if ($limit < 0 || $limit > 100) {
            throw new \Exception('Invalid argument: "limit" size range: 1-100');
        }

        $is_team = $is_team ? 'true' : 'false';

        $url = $this->base_url . "/analysis/?team=${is_team}&skip=${skip}&limit=${limit}";
        $history = WebClient::get($url, $this->authorization);

        $history = json_decode($history, true);
        return $history;
    }

    /**
     * https://any.run/api-documentation/#api-Analysis-GetReport
     *
     * @access public
     *
     * @param string $task_id
     *
     * @return array $report
     */
    public function get_report(string $task_id): array
    {
        if (!preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $task_id)) {
            throw new \Exception('Invalid argument: "task_id" is UUID');
        }

        $url = $this->base_url . "/analysis/?${task_id}";
        $report = WebClient::get($url, $this->authorization);

        $report = json_decode($report, true);
        if (!isset($report['data']['tasks'])) {
            if (!isset($report['data']['status'])) {
                throw new \Exception('Error: ' . json_encode($report));
            } else {
                throw new \Exception('Error: ' . $report['data']['status']);
            }
        }

        return $report['data']['tasks'];
    }

    /**
     * https://any.run/api-documentation/#api-Analysis-PostAnalysis
     *
     * @access public
     *
     * @param string $file_path
     * @param array $options
     *
     * @return string $task_id
     */
    public function post_analysis(string $target, array $options): string
    {
        if (!isset($options['obj_type'])) {
            throw new \Exception('Invalid argument: set obj_type (file, link, download)');
        }

        $options['obj_type'] = strtolower($options['obj_type']);

        $url = $this->base_url . '/analysis';

        if ($options['obj_type'] === 'file') {
            if (!file_exists($target)) {
                throw new \Exception('File not found');
            }

            $result = WebClient::post_file($url, $target, $options, $this->authorization);
        } else if ($options['obj_type'] === 'url') {
            if (filter_var($target, FILTER_VALIDATE_URL) === false) {
                throw new \Exception('URL is not valid');
            }

            $options['obj_url'] = $target;
            $result = WebClient::post($url, $options, $this->authorization);
        } else if ($options['obj_type'] === 'dowload') {
            if (filter_var($target, FILTER_VALIDATE_URL) === false) {
                throw new \Exception('URL is not valid');
            }

            $options['obj_url'] = $target;
            $result = WebClient::post($url, $options, $this->authorization);
        } else {
            throw new \Exception('Invalid argument: set obj_type (file, link, download)');
        }

        $result = json_decode($result, true);
        if (!isset($result['data']['taskid'])) {
            if (!isset($result['message'])) {
                throw new \Exception('Error: ' . json_encode($result));
            } else {
                throw new \Exception('Error: Status => ' . $result['message']);
            }
        }

        return $result['data']['taskid'];
    }

    /**
     * https://any.run/api-documentation/#api-Environment-GetEnvironment
     *
     * @access public
     *
     * @return array $env
     */
    public function get_env(): array
    {
        $url = $this->base_url . '/environment';
        $env = WebClient::get($url, $this->authorization);

        $env = json_decode($env, true);
        if (!isset($env['data']['environments'])) {
            if (!isset($env['message'])) {
                throw new \Exception('Error: ' . json_encode($env));
            } else {
                throw new \Exception('Error: Status => ' . $env['message']);
            }
        }

        return $env['data']['environments'];
    }

    /**
     * https://any.run/api-documentation/#api-User-GetLimits
     *
     * @access public
     *
     * @return array $user_limits
     */
    public function get_limits(): array
    {
        $url = $this->base_url . '/user';
        $user_limits = WebClient::get($url, $this->authorization);

        $user_limits = json_decode($user_limits, true);
        if (!isset($user_limits['data']['limits'])) {
            if (!isset($user_limits['message'])) {
                throw new \Exception('Error: ' . json_encode($user_limits));
            } else {
                throw new \Exception('Error: ' . $user_limits['message']);
            }
        }

        return $user_limits['data']['limits'];
    }
}
