<?php

namespace Models;

use Buzz\Browser;

class Link extends AbstractModel
{
    public const TABLE_NAME = 'links';

    public const TYPE_TEXT   = 'text';
    public const TYPE_IMAGES = 'images';
    public const TYPE_LINKS  = 'links';

    protected $id;

    protected $url;

    protected $type;

    protected $data;

    protected $cnt;

    protected $text;

    protected $properties = [
        'id',
        'url',
        'type',
        'data',
        'cnt',
        'text'
    ];

    public function getId()
    {
        return $this->id;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getData()
    {
        return json_decode($this->data, true);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCnt()
    {
        return $this->cnt;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    protected function setData($data)
    {
        $this->data = json_encode($data);
        return $this;
    }

    public function setType($type)
    {
        $this->type = in_array($type, [static::TYPE_LINKS, static::TYPE_IMAGES, static::TYPE_TEXT]) ?
            $type :
            null;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    protected function setCnt($cnt)
    {
        $this->cnt = $cnt;
        return $this;
    }

    public function save()
    {
        $this
            ->setText($this->type == static::TYPE_TEXT ?
                $this->text :
                ''
            )
            ->setData($this->fillData())
            ->setCnt(count($this->getData()));

        return parent::save();
    }

    protected function fillData()
    {
        $browser = new Browser();
        $response = $browser->get($this->url)->__toString();

        $matches = [];
        $result = [];
        switch ($this->type) {
            case static::TYPE_LINKS:
                preg_match_all('/<a[^>]+href="(\/|http[^"]+)"/i', $response, $matches);
                $result = $matches[1] ?? [];
                break;
            case static::TYPE_IMAGES:
                preg_match_all('/<img[^>]+src="([^"]+)"/i', $response, $matches);
                $result = $matches[1] ?? [];
                break;
            default:
                preg_match_all('/' . preg_quote($this->text, '/') . '/i', $response, $matches);
                $result = $matches[0] ?? [];
                break;
        }

        // convert relative URLs to absolute
        if (in_array($this->type, [static::TYPE_LINKS, static::TYPE_IMAGES])) {
            $parsedUrl = parse_url($this->url);
            $scheme   = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : 'http://';
            $host     = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
            $port     = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
            $user     = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
            $pass     = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass']  : '';
            $pass     = ($user || $pass) ? "$pass@" : '';
            $path     = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
            $defaultUrl = $scheme . $user . $pass . $host . $port . $path;

            if (substr($defaultUrl, -1) == '/') {
                $defaultUrl = substr($defaultUrl, 0, -1);
            }

            foreach ($result as &$value) {
                if (! preg_match('/^https?:\/\//', $value)) {
                    if (strpos('/', $value) !== 0) {
                        $value = '/' . $value;
                    }
                    $value = $defaultUrl . $value;
                }

            }
        }

        return $result;
    }
}