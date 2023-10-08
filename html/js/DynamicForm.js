class DynamicForm {
    constructor(form, container, addFunction, removeFunction, submitFunction) {
        this.inputStack = [];
        this.form = form;   // html form
        this.container = container;
        
        this.uniqueId = this.form.id.slice(3); // get 2nd half of id

        this.addButton = this.form.querySelector("#mb-add-" + this.uniqueId);
        this.removeButton = this.form.querySelector("#mb-remove-" + this.uniqueId);  
        this.confirmButton = this.form.querySelector("#mb-confirm-" + this.uniqueId);

        this.addButton.addEventListener("click", addFunction.bind(this));
        this.removeButton.addEventListener("click", removeFunction.bind(this));
        this.confirmButton.addEventListener("click", submitFunction.bind(this));
    }
}