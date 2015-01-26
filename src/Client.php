<?php

namespace Drupal\ParseComposer;

use Guzzle\Http\Client as BaseClient;

/**
 * Modified guzzle client to parse xml.
 */
class Client extends BaseClient
{
    /**
     * {@inheritdoc}
     */
    public function get($uri = null, $headers = null, $options = array())
    {
        return parent::get($uri, $headers, $options)->send()->xml();
    }
}
