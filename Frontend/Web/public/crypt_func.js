export async function masterkey(email, masterp){   //PBKDF2-SHA256 - 100000 iterations - salt: email address - payload: master password
    let result = await getKeyMP(masterp);
    let mk = await getMasterkey(email, result);
    return mk;
}
export async function hash_mp(masterp, masterk){ //PBKDF2-SHA256 - 1 iterations - salt: master password - payload: master key
    let result = await getKeyMP(masterk);
    let hmp = await getHashMasterPassword(masterp, result);
    return hmp;
}
export async function final_mp_fir(salt, hashmp){    //PBKDF2-SHA256 - 100000 iterations - salt: pseudorandom() - payload: master password hash
    let result = await getKeyMP(hashmp);
    let final = await getFinal(salt, result);
    return final;
}
async function getKeyMP(key){
    return window.crypto.subtle.importKey("raw", new TextEncoder().encode(key), {name: "PBKDF2"}, true, ["deriveBits", "deriveKey"]);
}
async function getMasterkey(email, key){
    return window.crypto.subtle.deriveKey(
        {
          "name": "PBKDF2",
          salt: new TextEncoder().encode(email),
          "iterations": 100000,
          "hash": "SHA-256"
        },
        key,
        { "name": "AES-GCM", "length": 256},
        true,
        ["encrypt", "decrypt"]
      );
}
async function getHashMasterPassword(masterp, key){
    return window.crypto.subtle.deriveKey(
        {
          "name": "PBKDF2",
          salt: new TextEncoder().encode(masterp),
          "iterations": 1,
          "hash": "SHA-256"
        },
        key,
        { "name": "AES-GCM", "length": 256},
        true,
        ["encrypt", "decrypt"]
      );
}
async function getFinal(salt, key){
    return window.crypto.subtle.deriveKey(
        {
          "name": "PBKDF2",
          salt: new TextEncoder().encode(salt),
          "iterations": 100000,
          "hash": "SHA-256"
        },
        key,
        { "name": "AES-GCM", "length": 256},
        true,
        ["encrypt", "decrypt"]
      );
}
export async function stretched_mk(masterk){    //HKDF
    
}

export async function pseudorandom(){    //CSPRNG
    return CryptoJS.randomBytes(20, (err, buffer) => {
        const token = buffer.toString('hex');
        console.log(token);
      });
}

function str2ab(str) {
    var salt = window.crypto.getRandomValues(new Uint8Array(16));


    var buf = new ArrayBuffer(str.length*2); // 2 bytes for each char
    var bufView = new Uint16Array(buf);
    for (var i=0, strLen=str.length; i < strLen; i++) {
    bufView[i] = str.charCodeAt(i);
    }
    return buf;
}