<?php
// Test this using following command
// php -S localhost:8080 ./index.php &
// curl http://localhost:8080 -d '{"query": "query { authUser(UserName: \"Fofo\", Password: \"123\") }" }' &
// curl http://localhost:8080 -d '{"query": "query { postUser(UserName: \"Fofo\", Password: \"123\") }" }' &
// curl http://localhost:8080 -d '{"query": "query { updateUser(UserName: \"Fofo\", Password: \"123\") }" }'

// Project requirements
require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;


// Connect to mysql
$conn = pg_connect('postgres://jitxsjif:GnL5MazZMd2aw3j_BUvb4GYwcjLv0VXv@isilo.db.elephantsql.com:5432/jitxsjif') or die ("Could not connect to server\n");


try {
    // Define queryType ObjectType
    $queryType = new ObjectType([
        'name' => 'Query',
        'fields' => [
            'sign_in' => [
                'type' => Type::string(),
                'args' => [
                    'UserName' => ['type' => Type::string()],
                    'Password' => ['type' => Type::string()],
                ],
                'resolve' => function($root, $args) {
                    $UserName = $args['UserName'];
                    $Password = $args['Password'];
                    
                    global $conn;

                    $sql = "SELECT Id, FirstName FROM users WHERE UserName = '$UserName' AND Password = crypt('$Password', Password)";
                    $result = pg_query($conn, $sql);
                    if (!$result) {
                        echo "An error occurred.\n";
                        return 0;
                    }else{
                        $row = pg_fetch_row($result);
                        echo "Welcome!: " . $row[1] . "\n";
                        return $row;
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
                    'UserName' => ['type' => Type::string()],
                    'FirstName' => ['type' => Type::string()],
                    'LastName' => ['type' => Type::string()],
                    'Password' => ['type' => Type::string()],
                ],
                'resolve' => function($root, $args) {
                    $UserName = $args['UserName'];
                    $FirstName = $args['FirstName'];
                    $LastName = $args['LastName'];
                    $Password = $args['Password'];
                    

                    global $conn;


                    $sql = "INSERT INTO users(UserName,FirstName,LastName,Password) VALUES ('$UserName', '$FirstName', '$LastName', crypt('$Password', gen_salt('bf')))";

                    $result = pg_query($conn, $sql);
                    if (!$result) {
                        echo "An error occurred.\n";
                        return 0;
                    }else{
                        echo "User created" . "\n";
                        return $UserName;

                    }
                    
                    
                },
            ],
            'update_user' => [
                'type' => Type::string(),
                'args' => [
                    'UserName' => ['type' => Type::string()],
                    'FirstName' => ['type' => Type::string()],
                    'LastName' => ['type' => Type::string()],
                    'Password' => ['type' => Type::string()],
                ],
                'resolve' => function($root, $args) {
                    $UserName = $args['UserName'];
                    $FirstName = $args['FirstName'];
                    $LastName = $args['LastName'];
                    $Password = $args['Password'];

                    global $conn;

                    //Set sql statement
                    $sql = "UPDATE users SET UserName = '$UserName', FirstName = '$FirstName', LastName = '$LastName' WHERE Password = crypt('$Password', Password) ";
                   
                    $result = pg_query($conn, $sql);
                    if (!$result) {
                        echo "An error occurred.\n";
                        return 0;
                    }else{
                        echo "User updated" . "\n";
                        return $UserName;
                    }

                },
            ],
            'delete_user' => [
                'type' => Type::string(),
                'args' => [
                    'UserName' => ['type' => Type::string()],
                    'Password' => ['type' => Type::string()],
                ],
                'resolve' => function($root, $args) {
                    $UserName = $args['UserName'];
                    $Password = $args['Password'];
                    
                    global $conn;

                    $sql = "DELETE FROM users WHERE UserName = '$UserName' AND Password = crypt('$Password', Password)";
                    $result = pg_query($conn, $sql);
                    if (!$result) {
                        echo "An error occurred.\n";
                        return 0;
                    }else{
                        echo "User Deleted" . "\n";
                        return $UserName;
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
} catch(\Exception $e) {
    $output = [
        'error' => [
            'message' => $e->getMessage()
        ]
    ];
}

header('Content-Type: application/json; charset=UTF-8');