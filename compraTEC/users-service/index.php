<?php
// Test this using following command
// php -S localhost:8080 ./index.php
// Project requirements
require_once __DIR__ . '/vendor/autoload.php';

// Use dependencies  of Grap
include_once 'src/BeforeValidException.php';
include_once 'src/ExpiredException.php';
include_once 'src/JWT.php';
include_once 'src/SignatureInvalidException.php';

use GraphQL\Type\Schema;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\GraphQL;
use \Firebase\JWT\JWT;

// Connect to ElephantSQL
$conn = pg_connect('postgres://jitxsjif:GnL5MazZMd2aw3j_BUvb4GYwcjLv0VXv@isilo.db.elephantsql.com:5432/jitxsjif') or die("Could not connect to server\n");

//allowed IP. Change it to your static IP
$allowedip = '127.0.0.1';
$ip = $_SERVER['REMOTE_ADDR'];

if ($ip == $allowedip) {
  try {
    // Define queryType ObjectType
    $queryType = new ObjectType([
      'name' => 'Query',
      'fields' => [
        'sign_in' => [
          'type' => Type::string(),
          'args' => [
            'Password' => ['type' => Type::string()],
            'UserName' => ['type' => Type::string()],
          ],
          'resolve' => function ($root, $args) {
            $Password = $args['Password'];
            $UserName = $args['UserName'];

            $sql = "SELECT Id, FirstName FROM users WHERE UserName = '$UserName' AND Password = crypt('$Password', Password)";
            $result = pg_query($GLOBALS['conn'], $sql);
            if (!$result) {
              echo "Query did not execute \n";
              return "Query did not execute \n";
            }
            if (pg_num_rows($result) == 0) {
              echo "Error: Invalid User Name or Password \n";
              return "Error: Invalid User Name or Password \n";
            } else {
              $key = "soa_key";
              $token = array(
                "iss" => "http://soa.org",
                "aud" => "http://soa.com",
                "iat" => 1356999524,
                "nbf" => 1357000000,
              );
              $jwt = JWT::encode($token, $key);
              $queryToken = "UPDATE users SET Token = '$jwt' WHERE UserName = '$UserName'";
              $resultToken = pg_query($GLOBALS['conn'], $queryToken);
              if (!$resultToken) {
                echo "Query did not execute \n";
                return "Query did not execute \n";
              } else {
                $row = pg_fetch_row($result);
                echo "Welcome: $row[1] \n";
                return "Welcome: $row[1] \n";
              }
            }
          }
        ],
      ],
    ]);

    $mutationType = new ObjectType([
      'name' => 'Mutation',
      'fields' => [
        'create_user' => [
          'type' => Type::string(),
          'args' => [
            'FirstName' => ['type' => Type::string()],
            'LastName' => ['type' => Type::string()],
            'Password' => ['type' => Type::string()],
            'UserName' => ['type' => Type::string()],
          ],
          'resolve' => function ($root, $args) {
            $FirstName = $args['FirstName'];
            $LastName = $args['LastName'];
            $Password = $args['Password'];
            $UserName = $args['UserName'];

            $sql = "INSERT INTO users(UserName,FirstName,LastName,Password) VALUES ('$UserName', '$FirstName', '$LastName', crypt('$Password', gen_salt('bf')))";
            $result = pg_query($GLOBALS['conn'], $sql);
            if (!$result) {
              echo "Query did not execute \n";
              return "Query did not execute \n";
            } else {
              echo "User created \n";
              return "User created \n";
            }
          },
        ],
        'update_user' => [
          'type' => Type::string(),
          'args' => [
            'FirstName' => ['type' => Type::string()],
            'LastName' => ['type' => Type::string()],
            'Password' => ['type' => Type::string()],
            'UserName' => ['type' => Type::string()],
          ],
          'resolve' => function ($root, $args) {
            $FirstName = $args['FirstName'];
            $LastName = $args['LastName'];
            $Password = $args['Password'];
            $UserName = $args['UserName'];

            $sql = "UPDATE users SET UserName = '$UserName', FirstName = '$FirstName', LastName = '$LastName' WHERE Password = crypt('$Password', Password) RETURNING Id";
            $result = pg_query($GLOBALS['conn'], $sql);
            if (!$result) {
              echo "Query did not execute \n";
              return "Query did not execute \n";
            }
            if (pg_num_rows($result) == 0) {
              echo "Error: Invalid Password \n";
              return "Error: Invalid Password \n";
            } else {
              echo "User updated \n";
              return "User updated \n";
            }
          },
        ],
        'delete_user' => [
          'type' => Type::string(),
          'args' => [
            'Password' => ['type' => Type::string()],
            'UserName' => ['type' => Type::string()],
          ],
          'resolve' => function ($root, $args) {
            $Password = $args['Password'];
            $UserName = $args['UserName'];

            $sql = "DELETE FROM users WHERE UserName = '$UserName' AND Password = crypt('$Password', Password) RETURNING Id";
            $result = pg_query($GLOBALS['conn'], $sql);
            if (!$result) {
              echo "Query did not execute \n";
              return "Query did not execute \n";
            }
            if (pg_num_rows($result) == 0) {
              echo "Error: Invalid User Name or Password \n";
              return "Error: Invalid User Name or Password \n";
            } else {
              echo "User Deleted \n";
              return "User Deleted \n";
            }
          }
        ],
      ],
    ]);

    // See docs on schema options:
    // http://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
    $schema = new Schema([
      'query' => $queryType,
      'mutation' => $mutationType
    ]);
    //gets the root of the sent json {"query":"query{accidentsData(...)}"}
    $rawInput = file_get_contents('php://input');
    //decodes the content as JSON
    $input = json_decode($rawInput, true);
    //takes the "query" property of the object
    $query = $input['query'];
    //checks if the input variables are a set
    $variableValues = isset($input['variables']) ? $input['variables'] : null;
    //calls the graphQL PHP libraty execute query with the prepared variables
    $result = GraphQL::executeQuery($schema, $query, null, null, $variableValues);
    //converts the result to a PHP array
    $output = $result->toArray();
  } catch (\Exception $e) {
    $output = [
      'error' => [
        'message' => $e->getMessage()
      ]
    ];
  }
}

header('Content-Type: application/json; charset=UTF-8');
