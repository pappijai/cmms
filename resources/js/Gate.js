export default class Gate{

    constructor(user){
        this.user = user;
    }

    // Identify if admin
    isAdmin(){
        return this.user.type == 'admin';
    }

    // Identify if user
    isUser(){
        return this.user.type == 'user';
    }



}