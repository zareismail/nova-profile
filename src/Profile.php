<?php

namespace Zareismail\Cards;

use Laravel\Nova\Card;

class Profile extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';
 
    /**
     * The resource to be used to resolve the profile field's. 
     *
     * @var string
     */
    public $resource = \App\Nova\User::class;
 
    /**
     * The callback that using to resolve user avatar. 
     *
     * @var string
     */
    public $avatarCallback = 'avatar';

    /**
     * Create a new element.
     *
     * @param  string|null  $component
     * @return void
     */
    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->avatarUsing(function($user) {
            return data_get($user, $this->avatarCallback);
        });
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'profile';
    }

    /**
     * Define the resource that should be used to resolve the profile field's.
     * 
     * @param  string $resource
     * @return $this          
     */
    public function resourceUsing(string $resource)
    { 
        $this->resource = $resource;

        return $this;
    }

    /**
     * Define the callback that should be used to resolve the user avatar.
     * 
     * @param  callable $avatar
     * @return $this          
     */
    public function avatarUsing(callable $avatarCallback)
    { 
        $this->avatarCallback = $avatarCallback;

        return $this;
    }  

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'avatar' => call_user_func($this->avatarCallback, $user = request()->user()),
            'resourceId' =>$user->getKey(),
            'resourceName' => forward_static_call([$this->resource, 'uriKey']),
        ]);
    }
}
