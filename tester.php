<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\Rekognition\RekognitionClient;

$picture = $_FILES['pic1']['name'];
$tmp_picture = $_FILES['pic1']['tmp_name'];
$bucketname = 'imagesregonition';

echo "the name of the file is " . $picture . '<br>';
echo "the temp name of the file is " . $tmp_picture . '<br>';

$s3 = new S3Client([
    'version' => 'latest',
    'region' => 'us-west-2',
    'credentials' => [
        'key'    => 'AKIAJP5ZKN7GPN7SZ27A',
        'secret' => '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',
    ]
]);

$rekog = new RekognitionClient([
    'version' => 'latest',
    'region' => 'us-west-2',
    'credentials' => [
        'key'    => 'AKIAJP5ZKN7GPN7SZ27A',
        'secret' => '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',
    ]
]);

//puts object in S3 bucket
try {
    $result = $s3->putObject([
        'Bucket' => $bucketname,
        'Key' => $picture,
        'SourceFile' => $tmp_picture,
        'ACL' => 'public-read'
    ]);
    echo "you uploaded " . $picture . '<br>';
 }
   catch (S3Exception $e){
        echo $e->getMessage() . PHP_EOL;
    }

//  //   
// $test_get = $_FILES['pic1']['name'];

// echo $test_get;

$photo = $s3->getObject([
'Bucket' => 'imagesregonition',
'Key' => $picture,
'Range' => 'bytes=0-9',


]);

print_r($photo->toArray());
$arrPhoto = json_encode( $photo->toArray(), 3 );

echo "<br>";

$return = $photo->search('Metadata[*].{Wait: effectiveUri}');
//$return_imp = implode($return[0]);
// $body = var_dump($photo['@metadata']['effectiveUri']);
 $body2 = $photo['@metadata']['effectiveUri'];

// echo $body2;
// print_r( $body );
echo "<br>";
echo $body2;

//figure out how to get this to open to a whole new window. 
echo "<a href='" . $body2 . "'>
link</a>";


echo "<br>";

// $julyCollection = $rekog->createCollection([
// 'CollectionId' => 'july_collection'
// ]);
$lower = strtolower($picture);
$lower2 = strtolower($tmp_picture);

//code to add pictures to
$collResult = $rekog->indexFaces([
    'CollectionId' => 'july_collection',
    'DetectionAttributes' => [

    ],

    'ExternalImageId' => $lower,
    
    'Image' => [
        'S3Object' => [
            'Bucket' => 'imagesregonition',
            'Name' => $picture,
            'version' => 'latest',
        ],
    ],
]);

$result = $rekog->listFaces([
    'CollectionId' => 'july_collection',
    'MaxResults' => 3,
]);

// echo $result;

echo "there are " . sizeof($result) . " photos in the collection";

$faceName =  $result->search('Faces[*].{TestName: ExternalImageId}');
echo "<br>";
//  var_dump($result);
foreach ($faceName as $value) {
    echo $value['TestName'];
    echo "<br>";
    
}
 $work = $faceName[0]['TestName'];

echo $work;
 
?>