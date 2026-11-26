function getStrands() {
    const form=document.getElementById("substrands");
    const strand=document.getElementById("strand_id");
    const token=document.getElementById("token");

    if(!form) return;

    if(strand) strand.value="";
    if(token) token.disabled=true;
    form.method="GET";
    form.action="/school/substrands";
    form.submit();
}

function getSubstrands() {
    const form = document.getElementById("substrands");
    const token = document.getElementById("token");

    if (!form) return;
    if (token) token.disabled = true;
    form.method = "GET";
    form.action = "/school/substrands";
    form.submit();
}

function saveSubstrand() {
    const form = document.getElementById("substrands");
    const token = document.getElementById("token");

    if (!form) return;
    if (token) token.disabled = false;
    form.method = "POST";
    form.action = "/school/substrands/save";
    form.submit();
}