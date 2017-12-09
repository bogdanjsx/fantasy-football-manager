# fantasy-football-manager

You will need to refactor stuff like:

\MongoClient to \MongoDB\Client
\MongoCollection to \MongoDB\Collection
\MongoClient->selectDB to \MongoDB\Client->selectDatabase
\MongoClient->listDBs to \MongoDB\Client->listDatabases
also output is not an array but an iterator, so you'll need to use iterator_to_array, along with edits to how you use the resulting object
\MongoCollection->getName to \MongoDB\Collection->getCollectionName
\MongoCollection->update to \MongoDB\Collection->updateOne or updateMany
\MongoCollection->remove to \MongoDB\Collection->deleteOne
\MongoCollection->batchInsert to \MongoDB\Collection->insertMany

Noi folosim http://php.net/manual/en/set.mongodb.php

Pune extension=php_mongodb.dll in php.ini din
	wamp64\bin\apache\apache2.4.27\bin\php.ini
	wamp64\bin\php\php5.6.31\php.ini

API reference: https://docs.mongodb.com/php-library/current/reference/