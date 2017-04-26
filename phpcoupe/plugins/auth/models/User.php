<?php

include_once APP . 'models' . DS . 'App.php';

include_once APP . 'models' . DS . 'Client_client.php';

include_once APP . 'models' . DS . 'User_client.php';

class UserModel extends AppModel
{

	public $schema = array(
	'user_id'=>array('type'=>'integer', 'size'=>5, 'primary'=>array('Client_client', 'User_client'), 'notnull'=>TRUE, 'sequence'=>1),
	'name'=>array('type'=>'string', 'size'=>75, 'notnull'=>TRUE),
	'password'=>array('type'=>'string', 'size'=>10),
	'blocked'=>array('type'=>'bool', 'size'=>1, 'notnull'=>TRUE, 'default'=>1),
	'email'=>array('type'=>'string', 'size'=>85, 'notnull'=>TRUE),
	'phone'=>array('type'=>'string', 'size'=>45)
	);

}
