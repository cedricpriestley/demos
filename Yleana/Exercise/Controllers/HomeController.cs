using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Script.Serialization;
using System.Web.Mvc;

using Exercise.Models;

namespace Exercise.Controllers
{
    public class HomeController : Controller
    {
        public ActionResult Index()
        {
            return View();
        }

        public string GetCustomers()
        {
            CustomerDataContext CustomerContext = new CustomerDataContext();
            JavaScriptSerializer serializer = new JavaScriptSerializer();

            var Customers = CustomerContext.Customers;

            string customers = serializer.Serialize(Json(Customers).Data);

            return customers;
        }

        public string SaveCustomer(Customer customer)
        {
            try
            {
                if (customer != null)
                {
                    CustomerDataContext CustomerContext = new CustomerDataContext();
                    CustomerContext.Customers.InsertOnSubmit(customer);
                    CustomerContext.SubmitChanges();
                }

                return "Customer saved to database!";
            }
            catch (Exception ex)
            {
                //return "Error: " + ex.Message;
                return "An error occurred.";
            }
        }
    }
}