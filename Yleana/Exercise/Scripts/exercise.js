var viewModel;

var CustomerDetail = function (data) {
    var self = this;

    self.CustomerID = ko.observable(data.CustomerID);
    self.CustomerName = ko.observable(data.CustomerName).extend({ required: true });
    self.ContactName = ko.observable(data.ContactName).extend({ required: true });
    self.Address = ko.observable(data.Address).extend({ required: true });
    self.City = ko.observable(data.City).extend({ required: true });
    self.PostalCode = ko.observable(data.PostalCode).extend({ required: true });
    self.Country = ko.observable(data.Country).extend({ required: true });

    self.errors = ko.validation.group(self);

    self.SaveCustomer = function () {

        if (self.errors().length == 0) {
            $.ajax({
                url: '/home/savecustomer',
                type: "post",
                contentType: "application/json; charset=utf-8",
                data: ko.mapping.toJSON(self),
                success: function (result) {

                    $.ajax({
                        url: '/home/getcustomers',
                        contentType: "application/json; charset=utf-8",
                        type: "post",
                        dataType: "json",
                        success: function (data) {

                            self.CustomerName('');
                            self.ContactName('');
                            self.Address('');
                            self.City('');
                            self.PostalCode('');
                            self.Country('');

                            $("form").trigger('reset');
                            viewModel.Customers(data);

                            self.errors.showAllMessages(false);
                        },
                        error: function (err) {
                            $('#view-customers-results').text(err.status + " - " + err.statusText);
                        }
                    })

                    $('#add-customer-results').text(result);

                },
                error: function (err) {
                    $('#add-customer-results').text(err.status + " - " + err.statusText);
                }
            });
        } else {
            self.errors.showAllMessages();
        }
    };
}

var ViewModel = function (data) {
    var self = this;

    self.Customers = ko.observableArray(data);

    ko.validatedObservable(ko.mapping.fromJS(data, self));
}