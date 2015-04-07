<?php
class SpySchema extends CakeSchema
{

    public function before($event = array())
    {
        return true;
    }

    public function after($event = array())
    {
        return true;
    }

    public $spied_auth_users = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null),
        'user_model' => array('type' => 'text', 'null' => true, 'default' => null),
        'client_ip' => array('type' => 'text', 'null' => true, 'default' => null),
        'referer' => array('type' => 'text', 'null' => true, 'default' => null),
        'user_agent' => array('type' => 'text', 'null' => true, 'default' => null),
        'timestamp' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
        )
    );
}
