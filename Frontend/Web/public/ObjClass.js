class elemento{
    constructor(_name, _username, _email, _password, _favourite, _url, _note){
        this.name=_name;
        this.username=_username;
        this.email=_email;
        this.password=_password;
        this.favourite=_favourite;
        this.url=_url;
        this.note=_note;
    }
    getName(){ return this.name; } 
    setName(_name){ this.name=_name; }

    getUsername(){ return this.username; } 
    setUsername(_username){ this.username=_username; }

    getEmail(){ return this.email; } 
    setEmail(_email){ this.email=_email; }

    getPassword(){ return this.password; } 
    setPassword(_password){ this.password=_password; }

    getFavourite(){ return this.favourite; } 
    setFavourite(_favourite){ this.favourite=_favourite; }

    getUrl(){ return this.url; } 
    setUrl(_url){ this.url=_url; }

    getNote(){ return this.note; } 
    setNote(_note){ this.note=_note; }
}
/*
//Creazione di un oggetto
var uno = new elemento("gmail", "", "lussana.andrea03@gmail.com", "prova", "", "");

//Serializzazione di un array
var arr = [uno, due];
var s = JSON.stringify(arr);

//Deserializzazione
var p = JSON.parse(s);

*/