## ADODb Unit Tests
This is a standalone PHPUnit Unit Tester for the [ADOdb](https://adodb.org) database abstraction layer. It runs using any supported ADOdb database and performs tests against the driver and data dictionary for the database in use. It can be easily configured to run against multiple, side-by-side installations of ADOdb. For that reason, ADOdb is not included in the composer file. It can be found at [Github](https:/github.com/adodb/adodb) or at [Packagist](https://packagist.org/packages/adodb/adodb-php)
### Installation
Easiest installed from Packagist as this will add PHPUnit as well, otherwise download the release code and use it
### Prerequsites
[PHPunit version 12+](https://phpunit.org) must be installed and running for this to work. Packagist will install it if you don't already have it.  An ADOdb installation must be installed somewhere on the local file system, but that doesn't need any special installation.
 - The unit tester requires access to a supported database via a configured ADOdb connection. The user requires privileges to add, change and delete tables, views and procedures as well as SELECT,CREATE,UPDATE,DELETE privileges on the created tables.
- To test the filesystem caching, write permission on the local filesystem is required
- To test memcache caching, access to a configured memcache server is required
### Current Coverage
<table>
<tr>
    <th></th>
    <th>Core</th>
    <th>Data<br>Dictionary</th>
    <th>Meta<br>Functions</th>
    <th>Caching</th>
    <th>Active<br>Record</th>
    <th>XmlSchema</th>
    <th>Session<br>Management</th>
    <th>Stored<br>Procedures</th>
    <th>Driver<br>Specific<br>Tests</th>
</tr>
<tr>
    <th>mysqli</th>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td></td>
    <td></td>
    <td>&#x2714;</td>
</tr>
<tr>
    <th>mssqlnative</th>
     <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
     <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td></td>
    <td></td>
    <td></td>
</tr>
<tr>
    <th>sqlite3</th>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td></td>
    <td></td>
     <td></td>
</tr>
<tr>
    <th>postgres9</th>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td></td>
    <td></td>
    <td>&#x2714;</td>
</tr>
<tr>
    <th>oci8</th>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td></td>
    <td></td>
    <td>&#x2714;</td>
</tr>       
<tr>
    <th>db2</th>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td> 
    <td>&#x2714;</td>
    <td></td>
    <td></td>
    <td>&#x2714;</td>
    <td></td>
</tr> 
<tr>
    <th>PDO</th>
   <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td> 
    <td>&#x2714;</td>
    <td></td>
    <td></td>
     <td></td>
</tr> 
</table>

### Setup
Configuration information for the tests is held in a configuration file **adodb-unittest.ini**. The file can be located anywhere in the PHP include path. A template file with sample settings is available in **/tools/samples**

### Setting Up The Configuration File 
#### ADOdb Section
Add the ADOdb section and set the base directory for the ADOdb installation to test. If an ADOdb installation is included in the composer file, then that installation will effectively capture the test paths and only that installation can be tested.

Setting the casing activates the ADODB_CASE value
Setting the forcemode activates the ADODB_FORCE_MODE value

````
[ADOdb]
directory=/opt/some/local/ADOdb/installation

;0 ADODB_ASSOC_CASE_LOWER
;1 ADODB_ASSOC_CASE_UPPER
;2 ADODB_ASSOC_CASE_NATIVE
casing=1;
;ADODB_FORCE_IGNORE',0
;ADODB_FORCE_NULL',1
;ADODB_FORCE_EMPTY',2
;ADODB_FORCE_VALUE',3
;ADODB_FORCE_NULL_AND_ZERO',4
forcemode=0;
````
#### Driver Section
Each database to test requires an entry. The entry name can be anything, the connection type is based on the driver entry in the section. You can add as many drivers to the configuration file as you want. The first driver found in the configuration file flagged **active** is tested.

````
[MySQL]
driver=mysqli
dsn=
host=mysql-server.com
user=root
password=somepassword
database=adodb-tester
debug=0
parameters=
active=1
````

<table>
<tr><th>Setting</th><th>Description</th></tr>
<tr><td>dsn</td><td>Either use a connection DSN or specify the parameters usual</td></tr>
<tr><td>host</td><td>The hostname associated with the database</td></tr>
<tr><td>user</td><td>The connection username</td></tr>
<tr><td>password</td><td>The connection password</td></tr>
<tr><td>debug</td><td>Sets the debug mode</td></tr>
<tr><td>parameters</td><td>To set parameters normally set by <b>setConnectionParameter()</b>>, create a string in format <b>key=value;</b> Note that the parameters cannot be defined as Driver constants, you must use the numeric or string equivalents</td></tr>
<tr><td>active</td><td>The test is run against the first driver where the <b>active</b> flag is set to true</td></tr>
</table>

#### Blob Section 
This section must be defined with the path names of 2 files: 
1. A binary file to test BLOB handling, such as a jpeg file that can be used for read-write testing. If you use a very large size file, it may measurably slow down the test. If set to false or the file name is invalid, all BLOB tests are skipped.
2. A Plain text file to test CLOB handling. If set to false or the file name is invalid, all CLOB tests are skipped 

````
[blob]
testBlob=c:/temp/someJpeg.jpg
testClob=c:/temp/someBigTextFile.txt
````
#### XMLschema Section 
This section must be explicitly enabled in the configuration file, with the skipXmlTests value set to 0, otherwise all tests in the section are skipped. Setting the value to 1 will also skip the tests.
Setting debug=1 in this section activates the extended debugging feature available in this module.

````
[xmlschema]
skipXmlTests=0
debug=0
````
#### Meta Section
Unless explicitly enabled, the test to create a new database using the **createDatabase** method is skipped as it requires CREATE DATABASE privilege on the DBMS. To enable this test, set the following section:
````
[meta]
skipDbCreation=0
````
#### Active Record System
````
[activerecord]
skipTests=0
extended=0
quotefieldnames=false|UPPER|LOWER|BRACKETS
````
To test active-recordx.inc.php, set the extended flag to 1

#### Caching Section
Unless explicitly enabled, cache functions such as **CacheExecute()** are skipped. Tests are supported using Filesystem based or memcache based caching. To activate this, add the following section to adodb-unittest.ini:

##### Filesystem Based Tests
````
[caching]
cacheMethod=1
cacheDir=c:/dev/cache
````
##### Memcache Based Tests
````
[cache]
cacheMethod=2
cacheHost=192.168.1.50
````
To disable cache tests while leaving the section in place, ''set cacheMethod=0''
#### Stored Procedures
Stored procedure testing must be explicitly enabled by adding a section and setting a parameter
````
[storedprocedures]
skipTests=0
````

##### Globals Section 
To test some date functions, the local timezone must be equal to the server timezone. To change the timezone temporarily for the test, set the following global parameter in adodb-unittest.ini. This should exactly match the format in php.ini.
````
[globals]
date.timezone = 'America/Denver'
````
Any parameter saved into the **[globals]** section will be set using ini_set()
### Test Execution 
Testing supports all of the standard PHPunit test methods. Every test must include the --bootstrap connection statement
#### Examples
##### Complete System
Approximately 80 tests with 1500 assertions
````
phpunit  /install-directory/ADOdb-unittest/src --bootstrap=/install-directory/ADOdb-unittest/tools/dbconnector.php 
````
##### Testing just the data dictionary functions
````
 phpunit  src/DataDict --bootstrap=tools/dbconnector.php 
````
##### Testing a single function
````
phpunit  src/Helpers/GetInsertSqlTest.php --bootstrap=tools/dbconnector.php 
````
#### Driver Specific Tests
If an ADOdb feature is not supported by all systems, e.g. MySQL bulk binding, the test is located in **/Drivers** directory
