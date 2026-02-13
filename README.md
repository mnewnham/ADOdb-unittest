### ADODb Unit Tests
This is a standalone PHPUnit Unit Tester for the [ADOdb](https://adodb.org) database abstraction layer. It runs using any supported ADOdb database and performs tests against the driver and data dictionary for the database in use. It can be easily configured to run against multiple, side-by-side installations of ADOdb. For that reason, ADOdb is not included in the composer file. It can be found at [Github](https:/github.com/adodb/adodb) or at [Packagist](https://packagist.org/packages/adodb/adodb-php)
#### Installation
This is an early code release. There is no composer file. Just clone the code and use it
#### Prerequsites
[PHPunit version 12+](https://phpunit.org) must be installed and running for this to work. An ADOdb installation must be installed somewhere on the local file system. 
 - The unit tester requires access to a supported database via a configured ADOdb connection. The user requires privileges to add, change and delete tables, views and procedures as well as SELECT,CREATE,UPDATE,DELETE privileges on the created tables.
- To test the filesystem caching, write permission on the local filesystem is required
- To test memcache caching, access to a configured memcache server is required
#### Current Coverage
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
     <td></td>
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
     <td></td>
</tr>       
<tr>
    <th>db2</th>
    <td>&#x2714;</td>
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
    <th>firebird</th>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td>&#x2714;</td>
    <td></td>
    <td>&#x2714;</td>
    <td></td>
    <td></td>
   <td></td>
</tr> 
 <tr>
    <th>PDO</th>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
     <td></td>
</tr> 
</table>

#### Setup
Configuration information for the tests is held in a configuration file **adodb-unittest.ini**. The file can be located anywhere in the PHP include path. A template file is available in **/tools/samples**

#### Setting Up The Configuration File 
##### ADOdb Section
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

##### Blob Section 
This section must be defined with the path name of a binary file, such as a jpeg file that can be used for read-write testing. If set to false, all blob tests are skipped.

````
[blob]
testBlob=c:/temp/someJpeg.jpg
````
##### XMLschema Section 
This section must be explicitly enabled in the configuration file, with the skipXmlTests value set to 0, otherwise all tests in the section are skipped. Setting the value to 1 will also skip the tests.
Setting debug=1 in this section activates the extended debugging feature available in this module.

````
[xmlschema]
skipXmlTests=0
debug=0
````
##### Driver Section
The driver configuration name can be anything, the connection is based on the driver name. You can add as many drivers to the configuration file as you want. Only the driver specified on the command line or the first driver found in the configuration file flagged **active** is tested.

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
<tr><td>password</td><td>he connection password</td></tr>
<tr><td>debug</td><td>Sets the debug mode</td></tr>
<tr><td>parameters</td><td>To set parameters normally set by <b>setConnectionParameter()</b>>, create a string in format <b>key=value;</b> Note that the parameters cannot be defined as ADOdb constants, you must use the numeric or string equivalents</td></tr>
<tr><td>active</td><td>The test is run against the first driver where the <b>active</b> flag is set to true</td></tr>
</table>

##### Meta Section
Unless explicitly enabled, the test to create a new database using the **createDatabase** method is skipped as it requires CREATE DATABASE privilege on the DBMS. To enable this test, set the following section:
````
[meta]
skipDbCreation=0
````
##### Caching Section
Unless explicitly enabled, cache functions such as **CacheExecute()** are skipped. Tests are supported using Filesystem based or memcache based caching. To activate this, add the following section to adodb-unittest.ini:

##### Active Record System
````
[activerecord]
skipTests=0
extended=0
````
To test active-recordx.inc.php, set the extended flag to 1

##### Filesystem
````
[caching]
cacheMethod=1
cacheDir=c:/dev/cache
````
##### Memcache Base
````
[cache]
cacheMethod=2
cacheHost=192.168.1.50
````
To disable cache tests while leaving the section in place, ''set cacheMethod=0''
##### Stored Procedures
Stored procedure testing must be explicitly enabled by adding a section and setting a parameter
````
[storedprocedures]
skipTests=0
````

##### Globals Section 
To test some date functions, the local timezone must be equal to the server timezone. To change the timezone temporarily for the test, set the following gloval parameter in adodb-unittest.ini. This should exactly match the format in php.ini.
````
[globals]
date.timezone = 'America/Denver'
````
Any parameter saved into the **[globals]** section will be set using ini_set()
##### Test Execution 
Testing supports all of the standard PHPunit test methods
````
 vendor/bin/phpunit  src/XmlSchemaTest.php --bootstrap=tools/dbconnector.php 
````
It is not possible to pass the driver name as a run-time argument in this version. The driver to test must have the **active** flag defined in the driver section.

#### Writing or Updating Tests

  * Tests should be compliant with PHPunit version 12 and higher
  * Tests should be compatible with all platforms supported by PHPunit
  * Tests should be written to [[https://www.php-fig.org/psr/psr-12/|PSR 12]] standards when possible
  * Wherever possible, write database agnostic tests. Only write driver specific tests when a feature is only supported by one or two DBMS
  * Only add driver specific tests to the test file specifically labeled for that driver, e.g. **MysqliDriverTest.php**
  * Do not add driver-based code branches inside the generic test code
 

</table>

