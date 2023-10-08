class AjaxJob {
    constructor(id, repaired, model, note, passcode, charge, receiveDate, pickupDate, customer, phone1, phone2, email, address, problems, priceEstimate, pickupEstimate) {
        this.id = id;
        this.repaired = repaired;
        this.model = model;
        this.note = note;
        this.passcode = passcode;
        this.charge = charge;
        this.receiveDate = receiveDate;
        this.pickupDate = pickupDate;

        this.customer = customer;
        this.phone1 = phone1;
        this.phone2 = phone2;
        this.email = email;
        this.address = address;

        this.priceEstimate = priceEstimate;
        this.pickupEstimate = pickupEstimate;

        this.problems = problems;   // array

        this.searchString = this.generateSearchString();

        this.applyChangesQuery = "UPDATE `repair_jobs_database`.`jobs` SET `pickup_date` = ?, `charge` = ?, `successful_repair` = ? WHERE (`job_id` = ?);";
    }

    applyChanges(newRepaired, newCharge, newPickupDate) {
        this.repaired = newRepaired;
        this.charge = newCharge;
        this.pickupDate = newPickupDate;

        // apply changes to database
        const request = new XMLHttpRequest();

        let getString = new AjaxQueryGetString(this.applyChangesQuery, [this.pickupDate, this.charge, this.repaired, this.id], "change");

        request.open("GET", getString.getString);
        request.send();
    }

    generateSearchString() {
        return "".concat(this.id, this.repaired, this.model, this.note, this.passcode, this.charge, this.receiveDate, this.pickupDate, this.customer, this.phone1, this.phone2, this.email, this.address, this.priceEstimate, this.pickupEstimate, this.problems.join("")).toLowerCase();
    }

    search(repaired, notRepaired, receiveDateStart, receiveDateEnd, pickupDateStart, pickupDateEnd, searchString) {
        // return true or false based on criteria given
        searchString = searchString.toLowerCase();

        // parse all dates
        receiveDateStart = Date.parse(receiveDateStart);
        receiveDateEnd = Date.parse(receiveDateEnd);
        pickupDateStart = Date.parse(pickupDateStart);
        pickupDateEnd = Date.parse(pickupDateEnd);
        let currentReceiveDate = Date.parse(this.receiveDate);
        let currentPickupDate = Date.parse(this.pickupDate);
        // set dates to 0 or infinite if undefined
        if(isNaN(receiveDateStart)) {
            receiveDateStart = 0;
        }
        if(isNaN(pickupDateStart)) {
            pickupDateStart = 0;
        }
        if(isNaN(receiveDateEnd)) {
            receiveDateEnd = Number.MAX_SAFE_INTEGER;
        }
        if(isNaN(pickupDateEnd)) {
            pickupDateEnd = Number.MAX_SAFE_INTEGER;
        }
        if(isNaN(currentReceiveDate)) {
            currentReceiveDate = Number.MAX_SAFE_INTEGER;
        }
        if(isNaN(currentPickupDate)) {
            currentPickupDate = Number.MAX_SAFE_INTEGER;
        }

        // must match all criterias
        if(this.searchString.includes(searchString) && (this.repaired == repaired || this.repaired != notRepaired) && (currentReceiveDate >= receiveDateStart && currentReceiveDate <= receiveDateEnd) && (currentPickupDate >= pickupDateStart && currentPickupDate <= pickupDateEnd)) {
            return true;
        }
        return false;
    }
}