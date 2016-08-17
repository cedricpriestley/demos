using System.Web;
using System.Web.Optimization;

namespace Exercise
{
    public class BundleConfig
    {
        // For more information on bundling, visit http://go.microsoft.com/fwlink/?LinkId=301862
        public static void RegisterBundles(BundleCollection bundles)
        {
            bundles.Add(new ScriptBundle("~/bundles/jquery").Include(
                        "~/Scripts/jquery.js"));

            bundles.Add(new ScriptBundle("~/bundles/knockout").Include(
                        "~/Scripts/knockout.js"));

            bundles.Add(new ScriptBundle("~/bundles/knockoutmapping").Include(
                        "~/Scripts/knockout.mapping.js"));

            bundles.Add(new ScriptBundle("~/bundles/knockoutvalidation").Include(
                        "~/Scripts/knockout.validation.js"));

            bundles.Add(new ScriptBundle("~/bundles/exercise").Include(
            "~/Scripts/exercise.js"));
        }
    }
}
