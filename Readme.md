1. create new namespace in yii code style (+)
2. add ability to disable proxy file creation for non production environment (+)

3. add resolvers
3.1 request argument resolver(default yii implementation) (+)
3.2 active record resolver(find callback, find argument, error handler)
3.3 module component resolver(?moduleName, component name)
3.4 container resolver(????)


4. add test tool and tests
5. write documentation 

6. select version or support type of proxy manager generator(not compatible by dependencies)
   zend-framework/code|laminas/code

7. describe the way, how directory for proxies must be created

later
1. console arguments resovler
1. add and cache action argument metadata(parse reflection only once)
?2. variadic arguments support
3. register custom object resolvers
4. composite pk support for ar resolver
