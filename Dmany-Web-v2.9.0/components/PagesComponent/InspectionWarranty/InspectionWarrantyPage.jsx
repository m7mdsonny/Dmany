"use client";
import { useEffect, useState } from "react";
import { useNavigate } from "@/components/Common/useNavigate";
import Layout from "@/components/Layout/Layout";
import BreadCrumb from "@/components/BreadCrumb/BreadCrumb";
import { useSelector } from "react-redux";
import { userSignUpData } from "@/redux/reducer/authSlice";
import { setIsLoginOpen } from "@/redux/reducer/globalStateSlice";
import { inspectionWarrantyApi } from "@/utils/api";
import { toast } from "sonner";
import { t } from "@/utils";
import PageLoader from "@/components/Common/PageLoader";
import {
  ShieldCheck,
  CheckCircle2,
  Clock,
  DollarSign,
  FileText,
  AlertCircle,
  Info,
  Sparkles,
  ArrowRight,
  XCircle,
} from "lucide-react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";

const InspectionWarrantyPage = ({ itemId, slug }) => {
  const { navigate } = useNavigate();
  const loggedInUser = useSelector(userSignUpData);
  const [isLoading, setIsLoading] = useState(true);
  const [productDetails, setProductDetails] = useState(null);
  const [inspectionOrder, setInspectionOrder] = useState(null);
  const [inspectionConfig, setInspectionConfig] = useState(null);
  const [showPaymentDialog, setShowPaymentDialog] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  useEffect(() => {
    if (!loggedInUser?.id) {
      setIsLoginOpen(true);
      navigate("/");
      return;
    }

    fetchData();
  }, [itemId, loggedInUser]);

  const fetchData = async () => {
    try {
      setIsLoading(true);
      
      // Fetch product details
      const productResponse = await inspectionWarrantyApi.getProductDetails({ itemId, slug });
      setProductDetails(productResponse?.data?.data);

      // Fetch inspection configuration
      const configResponse = await inspectionWarrantyApi.getInspectionConfig();
      setInspectionConfig(configResponse?.data?.data);

      // Check if there's an existing inspection order
      const orderResponse = await inspectionWarrantyApi.getInspectionOrder({ itemId });
      if (orderResponse?.data?.error === false && orderResponse?.data?.data) {
        setInspectionOrder(orderResponse.data.data);
        // If order exists, redirect to deal tracking page
        navigate(`/inspection-warranty/${itemId}/track?orderId=${orderResponse.data.data.id}`);
        return;
      }
    } catch (error) {
      console.error("Error fetching data:", error);
      toast.error(t("somethingWentWrong") || "Something went wrong");
    } finally {
      setIsLoading(false);
    }
  };

  const handleStartInspection = () => {
    if (!productDetails || !inspectionConfig) return;
    setShowPaymentDialog(true);
  };

  const handleConfirmPayment = async () => {
    try {
      setIsProcessing(true);
      const response = await inspectionWarrantyApi.createInspectionOrder({
        item_id: itemId,
      });

      if (response?.data?.error === false) {
        toast.success("Inspection order created successfully!");
        const orderId = response?.data?.data?.id;
        navigate(`/inspection-warranty/${itemId}/track?orderId=${orderId}`);
      } else {
        toast.error(response?.data?.message || "Failed to create inspection order");
      }
    } catch (error) {
      console.error("Error creating inspection order:", error);
      toast.error("Failed to create inspection order");
    } finally {
      setIsProcessing(false);
      setShowPaymentDialog(false);
    }
  };

  if (isLoading) {
    return (
      <Layout>
        <PageLoader />
      </Layout>
    );
  }

  if (!productDetails || !inspectionConfig) {
    return (
      <Layout>
        <div className="container mt-8">
          <p className="text-center text-muted-foreground">Product not found</p>
        </div>
      </Layout>
    );
  }

  const devicePrice = productDetails?.price || 0;
  const inspectionFeePercent = inspectionConfig?.fee_percentage || 4;
  const inspectionFee = (devicePrice * inspectionFeePercent) / 100;
  const totalAmount = devicePrice + inspectionFee;
  const warrantyDays = inspectionConfig?.warranty_duration || 5;

  return (
    <Layout>
      <BreadCrumb
        title2="Inspection & Warranty"
      />
      <div className="container mt-8 mb-12">
        {/* Hero Section */}
        <div className="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-2xl p-8 mb-8 animate-fadeIn">
          <div className="flex flex-col md:flex-row items-center gap-6">
            <div className="bg-blue-600 rounded-full p-4 trust-glow">
              <ShieldCheck className="w-12 h-12 text-white" />
            </div>
            <div className="flex-1 text-center md:text-left">
              <h1 className="text-3xl md:text-4xl font-bold mb-2 bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                Inspection & 5-Day Warranty
              </h1>
              <p className="text-lg text-muted-foreground">
                Buy with confidence. Professional inspection and warranty protection.
              </p>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left Column - Main Content */}
          <div className="lg:col-span-2 space-y-6">
            {/* A) Service Overview */}
            <Card className="animate-fadeInUp" style={{ animationDelay: "0.1s" }}>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Sparkles className="w-5 h-5 text-blue-600" />
                  How It Works
                </CardTitle>
                <CardDescription>
                  Your device will be professionally inspected before delivery
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                  {[
                    {
                      step: 1,
                      icon: DollarSign,
                      title: "Payment Secured",
                      description: "Your payment is held securely until inspection is complete",
                    },
                    {
                      step: 2,
                      icon: FileText,
                      title: "Device Inspected",
                      description: "Technician performs comprehensive USB & diagnostics check at our office",
                    },
                    {
                      step: 3,
                      icon: CheckCircle2,
                      title: "Delivered to You",
                      description: "Device is delivered with full inspection report",
                    },
                    {
                      step: 4,
                      icon: ShieldCheck,
                      title: "Warranty Active",
                      description: `${warrantyDays}-day warranty protection starts`,
                    },
                  ].map((item, index) => (
                    <div
                      key={item.step}
                      className="flex flex-col items-center text-center p-4 rounded-lg border bg-card animate-fadeIn"
                      style={{ animationDelay: `${0.2 + index * 0.1}s` }}
                    >
                      <div className="bg-blue-100 rounded-full p-3 mb-3">
                        <item.icon className="w-6 h-6 text-blue-600" />
                      </div>
                      <div className="text-xs font-semibold text-blue-600 mb-1">
                        STEP {item.step}
                      </div>
                      <h3 className="font-semibold text-sm mb-2">{item.title}</h3>
                      <p className="text-xs text-muted-foreground">{item.description}</p>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* B) What Is Covered */}
            <Card className="animate-fadeInUp" style={{ animationDelay: "0.2s" }}>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <ShieldCheck className="w-5 h-5 text-green-600" />
                  Warranty Coverage
                </CardTitle>
                <CardDescription>
                  What's covered and what's not in your {warrantyDays}-day warranty
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div>
                  <h3 className="font-semibold mb-3 flex items-center gap-2 text-green-700">
                    <CheckCircle2 className="w-5 h-5" />
                    Covered Issues
                  </h3>
                  <ul className="space-y-2 ml-7">
                    {(inspectionConfig?.covered_items || [
                      "Manufacturing defects discovered after delivery",
                      "Hardware malfunctions not caused by misuse",
                      "Battery health issues",
                      "Display defects",
                      "Performance issues",
                    ]).map((item, index) => (
                      <li key={index} className="flex items-start gap-2 text-sm">
                        <CheckCircle2 className="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span>{item}</span>
                      </li>
                    ))}
                  </ul>
                </div>

                <Separator />

                <div>
                  <h3 className="font-semibold mb-3 flex items-center gap-2 text-red-700">
                    <XCircle className="w-5 h-5" />
                    Not Covered
                  </h3>
                  <ul className="space-y-2 ml-7">
                    {(inspectionConfig?.excluded_items || [
                      "Damage caused by user (drops, water damage, etc.)",
                      "Cosmetic wear from normal use",
                      "Unauthorized modifications",
                      "Software issues from user-installed apps",
                      "Physical damage after delivery",
                    ]).map((item, index) => (
                      <li key={index} className="flex items-start gap-2 text-sm">
                        <XCircle className="w-4 h-4 text-red-600 mt-0.5 shrink-0" />
                        <span>{item}</span>
                      </li>
                    ))}
                  </ul>
                </div>

                <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div className="flex items-start gap-3">
                    <Info className="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <div className="text-sm text-blue-900">
                      <p className="font-semibold mb-1">Warranty Details</p>
                      <p>
                        The {warrantyDays}-day warranty period starts from the date of delivery.
                        All warranty claims must be reported within this period and will be
                        resolved by our support team.
                      </p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Right Column - Pricing & CTA */}
          <div className="space-y-6">
            {/* C) Pricing & Fees */}
            <Card className="sticky top-8 animate-fadeInUp" style={{ animationDelay: "0.3s" }}>
              <CardHeader>
                <CardTitle>Price Breakdown</CardTitle>
                <CardDescription>Clear, transparent pricing</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-3">
                  <div className="flex justify-between items-center">
                    <span className="text-muted-foreground">Device Price</span>
                    <span className="font-semibold">
                      ${devicePrice.toLocaleString()}
                    </span>
                  </div>
                  <Separator />
                  <div className="flex justify-between items-center">
                    <div className="flex items-center gap-2">
                      <span className="text-muted-foreground">
                        Inspection & Warranty Fee
                      </span>
                      <TooltipProvider>
                        <Tooltip>
                          <TooltipTrigger>
                            <Info className="w-4 h-4 text-muted-foreground cursor-help" />
                          </TooltipTrigger>
                          <TooltipContent>
                            <p className="max-w-xs">
                              The {inspectionFeePercent}% fee covers professional inspection,
                              diagnostics, and {warrantyDays}-day warranty protection.
                              Paid by buyer.
                            </p>
                          </TooltipContent>
                        </Tooltip>
                      </TooltipProvider>
                    </div>
                    <span className="font-semibold">${inspectionFee.toFixed(2)}</span>
                  </div>
                  <div className="text-xs text-muted-foreground ml-6">
                    ({inspectionFeePercent}% of device price)
                  </div>
                  <Separator />
                  <div className="flex justify-between items-center pt-2">
                    <span className="text-lg font-bold">Total Amount</span>
                    <span className="text-2xl font-bold text-blue-600">
                      ${totalAmount.toFixed(2)}
                    </span>
                  </div>
                </div>

                <Button
                  onClick={handleStartInspection}
                  className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-6 text-lg font-semibold btn-trust trust-glow-hover"
                  size="lg"
                >
                  <ShieldCheck className="w-5 h-5 mr-2" />
                  Start Inspection Process
                  <ArrowRight className="w-5 h-5 ml-2" />
                </Button>

                <p className="text-xs text-center text-muted-foreground">
                  Your payment is secured until inspection is complete
                </p>
              </CardContent>
            </Card>

            {/* Trust Badges */}
            <Card className="animate-fadeInUp" style={{ animationDelay: "0.4s" }}>
              <CardContent className="pt-6">
                <div className="space-y-4">
                  <div className="flex items-center gap-3">
                    <div className="bg-green-100 rounded-full p-2">
                      <CheckCircle2 className="w-5 h-5 text-green-600" />
                    </div>
                    <div>
                      <p className="font-semibold text-sm">Professional Inspection</p>
                      <p className="text-xs text-muted-foreground">
                        Expert technicians at our office
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="bg-blue-100 rounded-full p-2">
                      <ShieldCheck className="w-5 h-5 text-blue-600" />
                    </div>
                    <div>
                      <p className="font-semibold text-sm">{warrantyDays}-Day Warranty</p>
                      <p className="text-xs text-muted-foreground">
                        Full protection coverage
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="bg-purple-100 rounded-full p-2">
                      <Clock className="w-5 h-5 text-purple-600" />
                    </div>
                    <div>
                      <p className="font-semibold text-sm">Secure Payment</p>
                      <p className="text-xs text-muted-foreground">
                        Held until inspection complete
                      </p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>

      {/* Payment Confirmation Dialog */}
      <AlertDialog open={showPaymentDialog} onOpenChange={setShowPaymentDialog}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Confirm Inspection Order</AlertDialogTitle>
            <AlertDialogDescription>
              You are about to create an inspection order for this device. The total
              amount of <strong>${totalAmount.toFixed(2)}</strong> will be charged.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <div className="py-4 space-y-2">
            <div className="flex justify-between text-sm">
              <span>Device Price:</span>
              <span>${devicePrice.toLocaleString()}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span>Inspection & Warranty Fee:</span>
              <span>${inspectionFee.toFixed(2)}</span>
            </div>
            <Separator />
            <div className="flex justify-between font-semibold">
              <span>Total:</span>
              <span>${totalAmount.toFixed(2)}</span>
            </div>
          </div>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={isProcessing}>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={handleConfirmPayment}
              disabled={isProcessing}
              className="bg-blue-600 hover:bg-blue-700"
            >
              {isProcessing ? "Processing..." : "Confirm & Pay"}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </Layout>
  );
};

export default InspectionWarrantyPage;