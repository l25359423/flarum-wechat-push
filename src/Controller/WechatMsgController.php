<?php

namespace Leo\DailyMusic\Api\Controller;

use Leo\DailyMusic\Filter\DailyMusicFilter;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Flarum\Query\QueryCriteria;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Leo\DailyMusic\Api\Serializer\DailyMusicSerializer;

class WechatMsgController
{

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $file = fopen("/tmp/test.txt", "w");
        $data = file_get_contents("php://input");
        fwrite($file, $data);
        fclose($file);
    }
}
