var user = {};

function updateUserInfo(newUserInfo){
    if (newUserInfo.registered) {
        user.label = newUserInfo.label;
        user.roles = newUserInfo.roles;
        user.registered = newUserInfo.registered;
        user.email = newUserInfo.email;
        user.id = newUserInfo.id;
    }
}

function userResetInfo()
{
    user = {};
}

function userIsAdmin() {
    /**
    * Returns True if the user is admin.
    */
    return user.roles != undefined && user.roles.indexOf('ROLE_ADMIN') != -1;
}

function userIsRegister(){
    /**
    * Returns True if the user is register.
    */
    return user.registered != undefined && user.registered;
}

//setInterval( "checkUser()", 30000 ); //toutes les minutes -> checkUser() / 60000 -> i min

function userIsStillRegisterOnServer(){
    ret = undefined;
    $.getJSON(url_edit = Routing.generate('wikipedale_authenticate', {_format: 'json'}), function(data) {
        alert(data.results[0].registered);
        alert(data.results[0].id);
        alert(user.id);
        alert(data.results[0].id == user.id);
        if(data.results[0].registered && data.results[0].id == user.id)
            ret = true;
        else
            ret = false;
    });  
    return ret; 
}

function userIsAdminServer()
{
    $.getJSON(url_edit = Routing.generate('wikipedale_authenticate', {_format: 'json'}), function(data) {
        updateUserInfo(data.results[0]);
    });
    return userIsAdmin();
}