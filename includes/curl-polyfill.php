<?php

if (!extension_loaded('curl')) {
    function curl_init()
    {
        return new Curl();
    }

    function curl_setopt(&$ch, $option, $value)
    {
        $ch->setopt($option, $value);
    }

    function curl_getinfo(&$ch, $opt)
    {
        return $ch->getinfo($opt);
    }

    function curl_exec(&$ch)
    {
        return $ch->execute();
    }
    function curl_close(&$ch)
    {
        $ch->close();
    }

    class Curl
    {
        public function setopt($option, $value)
        {
            $this->{$option} = $value;
        }

        public function getinfo($opt)
        {
            return $this->{$opt};
        }

        public function close()
        {
            if ($this->fp) {
                fclose($this->fp);
                unset($this->fp);
            }
        }

        public function execute()
        {
            if ('array' == gettype($this->{CURLOPT_POSTFIELDS})) {
                $data = http_build_query($this->{CURLOPT_POSTFIELDS});
            } else {
                $data = $this->{CURLOPT_POSTFIELDS};
            }

            $options = ['http' => [
                'content' => $data,
            ],
            ];

            $method = 'GET';
            $method = $this->{CURLOPT_POST} ? 'POST' : $method;
            $method = $this->{CURLOPT_CUSTOMREQUEST} ? $this->{CURLOPT_CUSTOMREQUEST} : $method;
            if ('GET' != $method) {
                $options['http']['method'] = $method;
            }

            $options['http']['header'] = implode("\r\n", $this->{CURLOPT_HTTPHEADER});

            $ctx = stream_context_create($options);

            $fp = @fopen($this->{CURLOPT_URL}, 'rb', false, $ctx);
            $this->fb = $fp;

            try {
                if (!$fp) {
                    throw new Exception("Problem with {$this->{CURLOPT_URL}}, {$php_errormsg}");
                }
                $response = @stream_get_contents($fp);
                if (false === $response) {
                    throw new Exception("Problem reading data from {$this->{CURLOPT_URL}}, {$php_errormsg}");
                }

                $meta = @stream_get_meta_data($fp);

                $status = explode(' ', $meta['wrapper_data'][0]);
                $this->{CURLINFO_HTTP_CODE} = $status[1];

                if ($this->{CURLOPT_RETURNTRANSFER}) {
                    return $response;
                }

                echo $response;
            } catch (Exception $e) {
                print_r($options);
                $debug = debug_backtrace();
                print_r($debug);
                print_r($e->getMessage());
            }
        }
    }
}
