var user = {};

function updateUserInfo(newUserInfo){
    /**
    * Update the user informations contained locally in the JS.
    * @param newUserInfo contains the new informations (label, roles, registered, email, id)
    */
    if (newUserInfo.registered) {
        user = newUserInfo;
    }
}

function userResetInfo()
{
    /**
    * Remove all the user informations (contained locally in the JS). Must be used when the user logs out.
    */
    user = {};
}

function userIsAdmin() {
    /**
    * Returns True if the user is admin.
    */
    return user.roles != undefined && user.roles[0] != undefined && user.roles[0] == ("ROLE_ADMIN");
}

function userCanModifyCategories(){
    /**
    * True if the user can create or alter categories on a place.
    */
    return user.roles != undefined && user.roles[2] != undefined && user.roles[2] == ("ROLE_CATEGORY");
}

function userCanModifyLittleDetails()){
    /**
    * True if the user can alter details of a little point
    */
    return user.roles != undefined && user.roles[3] != undefined && user.roles[3] == ("ROLE_DETAILS_LITTLE");
}

function userCanVieuwUsersDetails(){
    /**
    * True if the user can see email and personal details of other users
    */
    return user.roles != undefined && user.roles[4] != undefined && user.roles[4] == ("ROLE_SEE_USER_DETAILS");

}

function userIsRegister(){
    /**
    * Returns True if the user is register.
    */
    return user.registered != undefined && user.registered;
}

function isUserInAccordWithServer(){
    /**
    * Returns True if the information contained locally in the JS is in accord with information in the server.
    * A difference happens when the session ends on the server but not in the js.
    */
    var defe = $.Deferred();
    if(userIsRegister()){
        $.getJSON(url_edit = Routing.generate('wikipedale_authenticate', {_format: 'json'}), function(data) {
            if(data.results[0].registered && data.results[0].id == user.id)
                {   
                    defe.resolve(true);
                }
            else
                {
                defe.resolve(false);
                }
        });  }
    else{  defe.resolve(true); }
    return defe;
}

function userIsAdminServer()
    /**
    * Returns True if the user is admin. The checking in by asking to the server.
    */
{
    $.getJSON(url_edit = Routing.generate('wikipedale_authenticate', {_format: 'json'}), function(data) {
        updateUserInfo(data.results[0]);
    });
    return userIsAdmin();
}
