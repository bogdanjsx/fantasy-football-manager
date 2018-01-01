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

Put in wamp64\bin\php\php5.6.31\ext the contents from php_mongodb-1.3.4-5.6-ts-vc11-x64.zip
Download it from http://pecl.php.net/package/mongodb

API reference: https://docs.mongodb.com/php-library/current/reference/


Hello to all football fans, we are here to bring you a live commentary of the following match, describing all the most interesting and important moments. We'll make sure you won't miss a thing, so just sit back and have fun.
The starting XIs were already announced and you can go through them in the Lineups section.