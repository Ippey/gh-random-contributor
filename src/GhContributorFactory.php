<?php

namespace Ippey\GhRandomContributor;

class GhContributorFactory
{
    public static function createWithArray(array $array)
    {
        $contributor = new GhContributor();
        $contributor
            ->setId($array['id'])
            ->setUsername($array['login'])
            ->setAvatarUrl($array['avatar_url'])
            ->setThumbnailUrl($array['avatar_url'] . '&s=80')
            ->setUrl($array['html_url'])
            ->setContributions($array['contributions'])
            ;

        return $contributor;
    }
}
