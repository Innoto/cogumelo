<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');

class UserViewModel extends Model {
  static $tableName = 'user_user_view';

  static $cols = [
    'id' => [
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ],
    'login' => [
      'type' => 'VARCHAR',
      'size' => '255',
      'unique' => true
    ],
    'password' => [
      'type' => 'VARCHAR',
      'size' => '255'
    ],
    'name' => [
      'type' => 'VARCHAR',
      'size' => '255'
    ],
    'surname' => [
      'type' => 'VARCHAR',
      'size' => '255'
    ],
    'email' => [
      'type' => 'VARCHAR',
      'size' => '255',
      'unique' => true
    ],
    'description' => [
      'type' => 'TEXT',
      'size' => '300',
      'multilang' => true
    ],
    'active' => [
      'type' => 'INT',
      'size' => '1'
    ],
    'verified' => [
      'type' => 'BOOLEAN',
      'default' => 0
    ],
    'timeLastLogin' => [
      'type' =>'DATETIME'
    ],
    'avatar' => [
      'type' =>'FOREIGN',
      'vo' => 'FiledataModel',
      'key' => 'id'
    ],
    'avatarName' => [
      'type' => 'VARCHAR',
      'size' => 250
    ],
    'avatarAKey' => [
      'type' => 'VARCHAR',
      'size' => 16
    ],
    'timeCreateUser' => [
      'type' => 'DATETIME'
    ],
    'timeLastUpdate' => [
      'type' => 'DATETIME'
    ],
    'hashUnknownPass' => [
      'type' => 'VARCHAR',
      'size' => '255'
    ],
    'hashVerifyUser' => [
      'type' => 'VARCHAR',
      'size' => '255'
    ],
    'loginTimeBan' => [
      'type' => 'DATETIME'
    ],
    'loginFailAttempts'=> [
      'type' => 'INT',
      'size' => '1'
    ],
    'role'=> [
      'type' => 'VARCHAR',
      'size' => '255'
    ]
  ];

  static $extraFilters = [
    'idIn' => ' user_user_view.id IN (?) ',
    'find' => "UPPER(user_user_view.surname)  LIKE CONCAT('%',UPPER(?),'%') OR user_user_view.login LIKE CONCAT('%', UPPER(?), '%')",
    'tableSearch' => " ( UPPER( user_user_view.name ) LIKE CONCAT( '%', UPPER(?), '%' ) OR UPPER( user_user_view.surname ) LIKE CONCAT( '%', UPPER(?), '%' ) OR UPPER( user_user_view.login ) LIKE CONCAT( '%', UPPER(?), '%' ) OR user_user_view.id = ? )",
    'roleFilter' => 'FIND_IN_SET(?, user_user_view.role) ',
  ];

  var $notCreateDBTable = true;
  var $deploySQL = [
    [
      'version' => 'user#6',
      'executeOnGenerateModelToo' => true,
      'sql'=> '
        DROP VIEW IF EXISTS user_user_view;
        CREATE VIEW user_user_view AS
          SELECT
            u.id, u.login, u.password, u.name, u.surname, u.email,
            {multilang:u.description_$lang,}
            u.active, u.verified, u.timeLastLogin,
            u.avatar, fd.name AS avatarName, fd.aKey AS avatarAKey,
            u.timeCreateUser, u.timeLastUpdate, u.hashUnknownPass, u.hashVerifyUser,
            u.loginTimeBan, u.loginFailAttempts,
            group_concat(
              ifnull(r.name, 0)
            ) AS role
          FROM
            user_user u
            LEFT JOIN filedata_filedata AS fd ON u.avatar = fd.id
            LEFT JOIN user_userRole AS ur
            ON ur.user = u.id
            LEFT JOIN user_role AS r
            ON r.id = ur.role
          GROUP BY
            u.id
      '
    ]
  ];

  public function deleteUser( $userId ) {
   return (new UserModel(['id' => $userId]))->delete();
  }
  public function userUpdateActive( $data ){
    $user = new UserModel(['id' => $data['userId']]);
    $user->setter('active', $data['value']);
    $user->save();
  }

}
