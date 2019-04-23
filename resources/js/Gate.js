export default class Gate{

    constructor(user){
        this.user = user;
    }

    // Identify if admin
    isSuperAdmin(){
        return this.user.type == 'super admin';
    }
    isRegistrarOnly(){
        return this.user.type == 'head of academics';
    }

    isHAFOnly(){
        return this.user.type == 'head of academics';
    }

    isAdminOnly(){
        return this.user.type == 'admin';
    }
    isAdministrativeOnly(){
        return this.user.type == 'administrative';
    }

    isAdmin(){
        return this.user.type == 'admin' || this.user.type == 'super admin';
    }

    // Identify if user
    isUser(){
        return this.user.type == 'user' || this.user.type == 'admin' || this.user.type == 'super admin';
    }

    isRegistrar(){
        return this.user.type == 'head of academics' || this.user.type == 'admin' || this.user.type == 'super admin';
    }

    isHAF(){
        return this.user.type == 'head of academics' || this.user.type == 'admin' || this.user.type == 'super admin';
    }

    isAdministrative(){
        return this.user.type == 'administrative' || this.user.type == 'admin' || this.user.type == 'super admin';
    }



}