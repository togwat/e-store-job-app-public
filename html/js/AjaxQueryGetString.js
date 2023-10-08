class AjaxQueryGetString {
    constructor(query, parameters, type) {
        this.getString = "php/ajaxQuery.php?";
        for(const parameter of parameters) {
            this.getString += `p[]=${parameter}&`;
        }
        this.getString += `q=${query}&`;
        this.getString += `t=${type}`;
    }
}