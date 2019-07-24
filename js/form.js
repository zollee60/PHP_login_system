const formElementProps = new Map();
formElementProps.set("surName", {name: "surName", input: "text", error: "names", label: "Keresztnév"});
formElementProps.set("lastName", {name: "lastName", input: "text", error: "names", label: "Vezetéknév"});
formElementProps.set("email", {name: "email", input: "text", error: "email", label: "E-mail cím"});
formElementProps.set("password", {name: "password", input: "password", error: "password", label: "Jelszó"});
formElementProps.set("confPassword", {name: "confPassword", input: "password", error: "confPassword", label: "Jelszó megerősítés"});

const formGroupElements = ["label", "input", "span"];

let session = {
    scope: "login",
    setScope: function (scope) {
        this.scope = scope;
        document.getElementById("form-wrapper").appendChild(createForm(scope));
    }
};

function createFormGroup(groupName){
    let g = document.createElement("div");
    g.className = "form-group";
    g.id = groupName+"G";
    let props = formElementProps.get(groupName);
    for (let i = 0; i < formGroupElements.length; i++){
        let fge = document.createElement(formGroupElements[i]);
        if(formGroupElements[i] === "label"){
            fge.innerHTML = props.label;
        }else if (formGroupElements[i] === "input"){
            fge.type = props.input;
            fge.name = props.name;
            fge.id = props.name;
            fge.className = "form-control";
            fge.required = true;
        } else{
            fge.className = "help-block";
            fge.id = props.name + "span";
        }
        g.appendChild(fge);
    }
    return g;
}

function createLink(scope){
    let text = document.createElement("p");
    let link = document.createElement("a");
    link.href = "#";
    link.id = "lr-link";
    text.id = "lr-text";
    if(scope === "login"){
        link.innerHTML = "Regisztráció";
        text.innerHTML = "Nincs még fiókod? ";
        link.addEventListener("click", function () {
            session.setScope("register");
        });
    }
    if(scope === "register"){
        link.innerHTML = "Bejelentkezés";
        text.innerHTML = "Már van fiókod? ";
        link.addEventListener("click", function () {
            session.setScope("login");
        });
    }
    text.appendChild(link);
    return text;
}

function process(scope) {
    let inputs = document.getElementsByTagName("input");
    let data = {scope: scope};
    for(let i = 0; i < inputs.length; i++){
        data[inputs[i].name] = inputs[i].value;
    }
    let dataJSON = JSON.stringify(data);
    let xhrSend = new XMLHttpRequest();
    xhrSend.onload = function () {};
    xhrSend.open("post", "process.php", true);
    xhrSend.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhrSend.send(dataJSON);

    let xhrGet = new XMLHttpRequest();
    xhrGet.onload = function () {
        let resData = JSON.parse(this.responseText);
        console.log(resData);
        Object.keys(resData).forEach(function (key) {
            let e = resData[key];
            if(document.getElementById(key+"span")!==null){
                let div = document.getElementById(key+"G");
                div.classList.add("has-error");
                let span = document.getElementById(key+"span");
                span.innerHTML = e;
            }
        });
    };
    xhrGet.open("get", "process.php", true);
    xhrGet.send();
}

function createForm(scope){
    if(document.getElementById("form")!==null){
        let form = document.getElementById("form");
        document.getElementById("form-wrapper").removeChild(form);
    }
    let form = document.createElement("form");
    form.method = "post";
    form.id = "form";
    let buttons = document.createElement("div");
    buttons.className = "form-group";
    let title = document.createElement("h2");
    if(scope === "login"){
        title.innerHTML = "Bejelentkezés";
        form.appendChild(title);
        form.appendChild(createFormGroup("email"));
        form.appendChild(createFormGroup("password"));
        let login = document.createElement("input");
        login.type = "submit";
        login.className = "btn btn-primary";
        login.value = "Bejelentkezés";
        login.id = "login";
        buttons.appendChild(login);

    }
    if(scope === "register"){
        title.innerHTML = "Regisztráció";
        form.appendChild(title);
        formElementProps.forEach(((value, key) => form.appendChild(createFormGroup(key))));
        let register = document.createElement("input");
        register.type = "submit";
        register.className = "btn btn-primary";
        register.value = "Regisztráció";
        register.id = "register";
        let reset = document.createElement("input");
        reset.type = "reset";
        reset.className = "btn btn-deafult";
        reset.value = "Törlés";
        buttons.appendChild(register);
        buttons.appendChild(reset);
    }
    let link = createLink(scope);
    form.appendChild(buttons);
    form.appendChild(link);
    form.addEventListener("submit", function () {
        process(scope);
    });

    return form;
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById("form-wrapper").appendChild(createForm(session.scope));
});




