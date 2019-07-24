const formElementProps = new Map();
formElementProps.set("surName", {name: "surName", input: "text", error: "names", label: "Keresztnév"});
formElementProps.set("lastName", {name: "lastName", input: "text", error: "names", label: "Vezetéknév"});
formElementProps.set("email", {name: "email", input: "text", error: "email", label: "E-mail cím"});
formElementProps.set("password", {name: "password", input: "password", error: "password", label: "Jelszó"});
formElementProps.set("confPassword", {name: "confPassword", input: "password", error: "confPassword", label: "Jelszó megerősítés"});

const formGroupElements = ["label", "input", "span"];

function process(scope){
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;
    let user = {email: email, password: password};
    if(scope === "register"){
        let surName = document.getElementById("surName").value;
        let lastName = document.getElementById("lastName").value;
        let confPassword = document.getElementById("confPassword").value;
        user.surName = surName;
        user.lastName = lastName;
        user.confPassword = confPassword;
    }
    let userJSON = JSON.stringify(user);
    let xhr = new XMLHttpRequest();
    let errorType;
    xhr.open("POST", "process.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
        if(xhr.readyState === 4 && xhr.status === 200) {
            let error = JSON.parse(xhr.responseText);
            if(!error.hasOwnProperty("noerror")){
                if(error.hasOwnProperty("email")) errorType = "password";
                if(error.hasOwnProperty("password")) errorType = "password";
                document.getElementById(errorType).classList.add("has-error");
                document.getElementById(errorType + "span").innerHTML = error[errorType];
            }
        }
    };
    xhr.send(userJSON);
}

function createFormGroup(groupName){
    let g = document.createElement("div");
    g.className = "form-group";
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

function createForm(scope){
    let form = document.createElement("form");
    form.method = "post";
    form.id = "form";
    let buttons = document.createElement("div");
    buttons.className = "form-group";
    let text = document.createElement("p");
    let link = document.createElement("a");
    if(scope === "login"){
        form.appendChild(createFormGroup("email"));
        form.appendChild(createFormGroup("password"));
        let login = document.createElement("input");
        login.type = "submit";
        login.className = "btn btn-primary";
        login.value = "Bejelentkezés";
        login.id = "login"
        text.innerHTML = "Nincs még fiókod?";
        link.href = "register.php";
        link.innerHTML = "Regisztráció";
        buttons.appendChild(login);
    }
    if(scope === "register"){
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
        text.innerHTML = "Már van fiókod?";
        link.href = "login.php";
        link.innerHTML = "Bejelentkezés";
        buttons.appendChild(register);
        buttons.appendChild(reset);
    }
    text.appendChild(link);
    form.appendChild(buttons);
    form.appendChild(text);

    return form;
}


document.addEventListener('DOMContentLoaded', function () {
    let scope;
    if(document.URL.includes("login")){
        scope = "login";
    } else {
        scope = "register";
    }
    document.getElementById("form-wrapper").appendChild(createForm(scope));
    if(scope === "register"){
        document.getElementById("register").addEventListener("click",function () {
            process(scope);
        })
    }else{
        document.getElementById("login").addEventListener("click",function () {
            process(scope);
        })
    }
});


