document.addEventListener("DOMContentLoaded",()=>{
    const form=document.getElementById("strands");
    const viewBtn=document.getElementById("viewStrands");
    const registerBtn=document.getElementById("register");
    const token=document.getElementById("token");

    if(!form) return;

    viewBtn?.addEventListener("click",()=>{
        form.method="GET";
        form.action="/school/strands";
        if(token) token.disabled=true;
    });

    registerBtn?.addEventListener("click",()=>{
        form.method="POST";
        form.action="/school/strands/save"
        if(token) token.disabled=false;
    });
});