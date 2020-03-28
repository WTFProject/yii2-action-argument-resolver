1. create new namespace in yii code style (+)
2. add ability to disable proxy file creation for non production environment (+)

3. add resolvers
3.1 request argument resolver(default yii implementation) (+)
3.2 active record resolver(find callback, find argument, error handler callback) (+)
3.3 module component resolver(?moduleName, component name) (+)
3.4 container resolver(????)
3.5 typed request attribute resolver - add boolean support and tests for this case (+)
3.6 ar resolver - add type check (+)

4. add test tool and tests
5. write documentation 
 5.1 describe the way, how directory for proxies must be created

later
1. console arguments resovler
2. add and cache action argument metadata(parse reflection only once)
3. composite pk support for ar resolver
?4. variadic arguments support
