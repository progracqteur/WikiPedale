define(['jQuery','params'], function($,params) {
    // the data of the user
    var u = {};

    function update(newUserData) {
        /**
        * Update the user informations contained locally in the module.
        * @param newUserInfo contains the new informations (label, roles, registered, email, id)
        */
        if (newUserData.registered) {
            u = newUserData;
        }
    }

    function reset() {
        /**
        * Remove all the user informations contained locally in the module.. To be used when the user logs out.
        */
        u = {};
    }

    function isAdmin() {
        /**
        * True if the user is admin.
        */
//        console.log(u);
        return (typeof u.roles !== "undefined") && $.inArray("ROLE_ADMIN", u.roles) !== -1;
    }

    function canModifyCategories() {
        /**
        * True if the user can create or alter categories on a place.
        */
        return (typeof u.roles !== "undefined") && $.inArray("ROLE_CATEGORY", u.roles) !== -1;
    }

    function canModifyLittleDetails() {
        /**
        * True if the user can alter details of a little point
        */
        return (typeof u.roles !== "undefined") && $.inArray("ROLE_DETAILS_LITTLE", u.roles) !== -1;
    }

    function canVieuwUsersDetails() {
        /**
        * True if the user can see email and personal details of other users
        */
        return (typeof u.roles !== "undefined") && $.inArray("ROLE_SEE_USER_DETAILS", u.roles) !== -1;
    }

    function canModifyPlacetype() {
        /**
        * True if the user can the place type of a point
        */
        return (typeof u.roles !== "undefined")  && $.inArray("ROLE_PLACETYPE_ALTER", u.roles) !== -1;
    }

    function canModifyManager() {
        /**
        * True if the user can the place type of a point
        */
        return (typeof u.roles !== "undefined") && $.inArray("ROLE_MANAGER_ALTER", u.roles) !== -1;
    }

    function canUnpublishADescription() {
        /**
        * True if the user can unpublish a description
        */
        return (typeof u.roles !== "undefined")  && $.inArray("ROLE_PUBLISHED", u.roles) !== -1;
    }

    function isModetatorForNotation(aNotation) {
        /**
        * True if the user is Moderator for the notation 'aNotation'
        */
        var ret = false;
        if ((typeof u.roles !== "undefined")  && $.inArray("ROLE_NOTATION", u.roles) !== -1) {
            if (typeof u.groups !== "undefined") {
                $.each(u.groups, function (id, data) {
                    if (data.type === "MODERATOR" && data.notation === aNotation) { //MODERATOR == CEM
                        ret = true;
                    }
                });
            }
        }
        return ret;
    }

    function isCeM() {
        /**
        * True is the user if Moderator for the notation params.manager_color
        */
       // console.log(u);
        return isModetatorForNotation(params.manager_color);
    }

    function isManagerForNotation(aNotation) {
        /**
        * True if the user is 'Gestionnaire de Voirie'
        */
        var ret = false;
        if ((typeof u.roles !== "undefined")  && $.inArray("ROLE_NOTATION", u.roles) !== -1) {
            if (typeof u.groups !== "undefined") {
                $.each(u.groups, function (id, data) {
                    if (data.type === "MANAGER" && data.notation === aNotation) { //MANAGER == Gestionnaire de VOIRIE
                        ret = true;
                    }
                });
            }
        }
        return ret;
    }

    function isGdV() {
        /**
        * True if the user is 'Gestionnaire de Voirie'
        */
        return isManagerForNotation(params.manager_color);
    }

    function isRegistered() {
        /**
        * Returns True if the user is registered regarding to the local informations.
        */
        return (typeof u.registered !== "undefined")  && u.registered;
    }

    function isInAccordWithServer() {
        /**
        * Returns A Defferred to know if the information contained locally in the JS is in accord with information in the server.
        * A difference happens when the session ends on the server but not in the js.
        */
        var defe = $.Deferred();
        if (isRegistered()) {
            $.getJSON(url_edit = Routing.generate('wikipedale_authenticate', {_format: 'json'}), function (data) {
                console.log(data);
                console.log(JSON.stringify(data));
                if (data.results[0].registered && data.results[0].id === u.id) {
                    defe.resolve(true);
                } else {
                    defe.resolve(false);
                }
            }); 
        } else {
            defe.resolve(true);
        }
        return defe;
    }

    function isAdminWithServerCheck() {
        /**
        * Returns True if the user is 'Admin' BUT AFTER updating the js local information from the server
        */
        $.getJSON(url_edit = Routing.generate('wikipedale_authenticate', {_format: 'json'}), function (data) {
            update(data.results[0]);
        });
        return isAdmin();
    }

    function data(){
        /**
        * Returns the data stored in local.
        */
        return u;
    }

    return {
        update: update,
        reset: reset,
        isAdmin: isAdmin,
        canModifyCategories: canModifyCategories,
        canModifyLittleDetails: canModifyLittleDetails,
        canVieuwUsersDetails: canVieuwUsersDetails,
        canModifyPlacetype: canModifyPlacetype,
        canModifyManager: canModifyManager,
        canUnpublishADescription: canUnpublishADescription,
        isModetatorForNotation: isModetatorForNotation,
        isCeM: isCeM,
        isManagerForNotation: isManagerForNotation,
        isGdV: isGdV,
        isRegistered: isRegistered,
        isInAccordWithServer: isInAccordWithServer,
        isAdminWithServerCheck: isAdminWithServerCheck,
        data: data
    };
});