// supports SelectQuery only
class AjaxDatalist {
    constructor(query, parameters, datalist) {
        this.query = query;
        this.parameters = parameters;   // array
        this.datalist = datalist;
    }

    createDatalist() {
        const request = new XMLHttpRequest();

        let getString = new AjaxQueryGetString(this.query, this.parameters, "select");

        request.open("GET", getString.getString);
        request.send();

        request.datalist = this.datalist;   // add datalist to request object

        // query finishes
        request.onload = function() {
            // create datalist options
            let result = JSON.parse(request.responseText);

            // remove all previous options
            while(this.datalist.firstChild) {
                this.datalist.removeChild(this.datalist.lastChild);
            }

            // add options to the list
            for(const model of result) {
                let option = document.createElement("option");
                option.value = model;
                this.datalist.appendChild(option);
            }
        }
    }

    setParameters(parameters) {
        this.parameters = parameters;
    }
}

// when there is an accompanying column in the data retrieved
class AjaxDatalistDictionary extends AjaxDatalist {
    constructor(query, parameters, datalist) {
        super(query, parameters, datalist);
    }

    createDatalist() {
        const request = new XMLHttpRequest();

        let getString = new AjaxQueryGetString(this.query, this.parameters, "select");

        request.open("GET", getString.getString);
        request.send();

        request.datalist = this.datalist;   // add datalist to request object
        // splits array into smaller dictionaries
        function splitArray(array, length) {
            let rows = [];
            let tempArray = [];
        
            for(let i = 0; i < array.length; i++) {
                // reset sub-array if sub-array size is reached
                if(i % length == 0) {
                    tempArray = [];
                }
                // add sub-array as row before reset
                else if(i % length == length - 1) {
                    tempArray.push(array[i]);
                    rows.push(tempArray);
                }
            
                tempArray.push(array[i]);
            }
            return rows;
        }

        // query finishes
        request.onload = function() {
            // create datalist options
            let result = JSON.parse(request.responseText);
            // split result into two rows
            result = splitArray(result, 2);
            // turn result into dictionary
            let resultDictionary = new Map(result);

            // remove all previous options
            while(this.datalist.firstChild) {
                this.datalist.removeChild(this.datalist.lastChild);
            }

            // create hidden input for prices
            let hidden = document.createElement("input");
            hidden.setAttribute("type", "hidden");
            hidden.setAttribute("id", "fi-hidden");
            // convert into array of arrays first
            hidden.setAttribute("value", JSON.stringify(Array.from(resultDictionary.entries())));
            this.datalist.appendChild(hidden);


            // add options to the list
            const resultIterator = resultDictionary[Symbol.iterator]();
            for(const row of resultIterator) {
                let option = document.createElement("option");
                option.value = row[0];
                this.datalist.appendChild(option);
            }
        }
    }
}