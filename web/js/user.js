var user = {};

function UpdateUserInfo(newUserInfo){
    if (newUserInfo.id != null || newUserInfo.label != null) {
        //alert(JSON.stringify(newUserInfo));
        //alert(JSON.stringify(user));
        user.label = newUserInfo.label;
        user.roles = newUserInfo.roles;
        user.registered = newUserInfo.registered;
        user.email = newUserInfo.email;
    }
}

function UserUpdatePassword(password){
    user.password = password;
}

function UserResetInfo()
{
    user = {};
}


function IsAdmin() {
    /**
    * Returns True if the user is admin.
    */
    return user.roles != undefined && user.roles.indexOf('ROLE_ADMIN') != -1;
}

function IsRegister(){
    /**
    * Returns True if the user is register.
    */
    return user.registered != undefined && user.registered;
}

setInterval( "checkUser()", 30000 ); //toutes les minutes -> checkUser() / 60000 -> i min

function  checkUser() // regarde si l'user est connecte
{
    $.getJSON(url_edit = Routing.generate('wikipedale_authenticate', {_format: 'json'}), function(data) {
        UpdateUserInfo(data.results[0]);
    });
}