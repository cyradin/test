<?php

namespace Models;

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

    protected $count;

    protected $text;

    protected $properties = [
        'id',
        'url',
        'type',
        'data',
        'count',
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

    public function getCount()
    {
        return $this->count;
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

    protected function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    public function save()
    {
        $this
            ->setCount(count($this->getData()))
            ->setText($this->type == static::TYPE_TEXT ?
                $this->text :
                ''
            )
            ->setData([]); // @TODO

        return parent::save();
    }
}