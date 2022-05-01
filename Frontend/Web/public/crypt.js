import * as func from './crypt_func.js'
export async function getmaster(email, masterp){
    const masterk = await func.masterkey(email, masterp);
    return masterk;
}
export async function final_mp(masterp, masterk){
    const hash_mp = await func.hash_mp(masterp, masterk);
    var final = await func.final_mp_fir("salt", hash_mp);
    return final;
}
export async function protec_simmkey(masterk){
    const stretched = await func.stretched_mk(masterk);
    console.log(stretched);
}
export async function RSA(){

}
/////////////////////////////////////////////////////
    /*const result = crypto.subtle.exportKey("raw", final);
    result.then(value=>{
        console.log(JSON.stringify(new Uint8Array(value).toString()));
    });*/
