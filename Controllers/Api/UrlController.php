<?php

namespace Controllers\Api;

use Lib\Validation\Validator;
use Symfony\Component\HttpFoundation\Request;

class UrlController extends AbstractApiController
{
    const TYPE_TEXT   = 'text';
    const TYPE_IMAGES = 'images';
    const TYPE_LINKS  = 'links';

    public function addAction(Request $request)
    {
        $fields = [
            'url' => $request->request->get('url'),
            'type' => $request->request->get('type')
        ];

        $rules = [
            'url' => [
                'notEmpty' => true,
                'regex'    => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'
            ],
            'type' => [
                'notEmpty' => true,
                'in'       => [
                    static::TYPE_TEXT,
                    static::TYPE_IMAGES,
                    static::TYPE_LINKS
                ]
            ]
        ];

        if ($fields['type'] == static::TYPE_TEXT) {
            $fields['text'] = $request->request->get('text');
            $rules['text']  = ['notEmpty' => true];
        }

        $validationResult = Validator::instance()->validate($fields, $rules);

        $response = ['status' => $validationResult->isSuccessful()];
        if (!$response['status']) {
            foreach ($validationResult->getErrors() as $code => $errors) {
                $response['errors'][$code] = implode('. ', $errors);
            }
        } else {
            $response['id'] = 1000;
        }

        $this->sendJson($response);
    }

    public function getAction(Request $request)
    {

    }

    public function listAction(Request $request)
    {

    }
}
