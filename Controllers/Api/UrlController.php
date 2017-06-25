<?php

namespace Controllers\Api;

use Lib\Validation\Validator;
use Models\Link;
use Symfony\Component\HttpFoundation\Request;

class UrlController extends AbstractApiController
{
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
                    Link::TYPE_TEXT,
                    Link::TYPE_IMAGES,
                    Link::TYPE_LINKS
                ]
            ]
        ];

        if ($fields['type'] == Link::TYPE_TEXT) {
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
            $data = $validationResult->getValues();

            $link = new Link();
            $link
                ->setUrl($data['url'])
                ->setType($data['type'])
                ->setText($data['text'] ?? '');

            if ($id = $link->save()) {
                $response['id'] = $id;
            } else {
                $response['status'] = false;
            }
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
