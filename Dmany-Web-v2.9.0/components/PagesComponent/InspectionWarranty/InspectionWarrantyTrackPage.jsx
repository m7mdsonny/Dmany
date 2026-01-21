"use client";
import { useEffect, useState } from "react";
import { useSearchParams } from "next/navigation";
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
  Package,
  FileText,
  AlertCircle,
  Download,
  Image as ImageIcon,
  FileDown,
  Truck,
  Award,
  XCircle,
} from "lucide-react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Progress } from "@/components/ui/progress";
import { Button } from "@/components/ui/button";

const InspectionWarrantyTrackPage = ({ itemId, orderId }) => {
  const searchParams = useSearchParams();
  const orderIdParam = orderId || searchParams.get("orderId");
  const { navigate } = useNavigate();
  const loggedInUser = useSelector(userSignUpData);
  const [isLoading, setIsLoading] = useState(true);
  const [inspectionOrder, setInspectionOrder] = useState(null);
  const [inspectionReport, setInspectionReport] = useState(null);

  useEffect(() => {
    if (!loggedInUser?.id) {
      setIsLoginOpen(true);
      navigate("/");
      return;
    }

    if (!orderIdParam) {
      toast.error("Order ID is required");
      navigate(`/inspection-warranty/${itemId}`);
      return;
    }

    fetchOrderData();
    // Poll for updates every 30 seconds
    const interval = setInterval(fetchOrderData, 30000);
    return () => clearInterval(interval);
  }, [orderIdParam, loggedInUser, itemId]);

  const fetchOrderData = async () => {
    try {
      setIsLoading(true);
      const response = await inspectionWarrantyApi.getInspectionOrder({
        itemId,
        orderId: orderIdParam,
      });

      if (response?.data?.error === false) {
        setInspectionOrder(response.data.data);
        if (response.data.data.inspection_report) {
          setInspectionReport(response.data.data.inspection_report);
        }
      } else {
        toast.error("Failed to fetch inspection order");
      }
    } catch (error) {
      console.error("Error fetching order data:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const getStatusInfo = (status) => {
    const statusMap = {
      pending: {
        label: "Waiting for Inspection",
        icon: Clock,
        color: "bg-yellow-100 text-yellow-800 border-yellow-300",
        step: 1,
      },
      under_inspection: {
        label: "Under Inspection",
        icon: FileText,
        color: "bg-blue-100 text-blue-800 border-blue-300",
        step: 2,
      },
      approved: {
        label: "Approved & Ready for Delivery",
        icon: CheckCircle2,
        color: "bg-green-100 text-green-800 border-green-300",
        step: 3,
      },
      delivered: {
        label: "Delivered",
        icon: Truck,
        color: "bg-purple-100 text-purple-800 border-purple-300",
        step: 4,
      },
      warranty_active: {
        label: "Warranty Active",
        icon: ShieldCheck,
        color: "bg-indigo-100 text-indigo-800 border-indigo-300",
        step: 5,
      },
      rejected: {
        label: "Inspection Failed",
        icon: XCircle,
        color: "bg-red-100 text-red-800 border-red-300",
        step: 0,
      },
    };

    return statusMap[status] || statusMap.pending;
  };

  const downloadReport = () => {
    if (inspectionReport?.report_url) {
      window.open(inspectionReport.report_url, "_blank");
    } else {
      toast.error("Report not available");
    }
  };

  if (isLoading && !inspectionOrder) {
    return (
      <Layout>
        <PageLoader />
      </Layout>
    );
  }

  if (!inspectionOrder) {
    return (
      <Layout>
        <div className="container mt-8">
          <p className="text-center text-muted-foreground">Inspection order not found</p>
        </div>
      </Layout>
    );
  }

  const statusInfo = getStatusInfo(inspectionOrder.status);
  const StatusIcon = statusInfo.icon;
  const progressPercentage = (statusInfo.step / 5) * 100;

  return (
    <Layout>
      <BreadCrumb title2="Inspection & Warranty Tracking" />
      <div className="container mt-8 mb-12">
        {/* Status Header */}
        <Card className="mb-8 animate-fadeInUp">
          <CardHeader>
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-4">
                <div className={`p-3 rounded-full ${statusInfo.color}`}>
                  <StatusIcon className="w-8 h-8" />
                </div>
                <div>
                  <CardTitle className="text-2xl">{statusInfo.label}</CardTitle>
                  <CardDescription>Order ID: {inspectionOrder.order_number || orderIdParam}</CardDescription>
                </div>
              </div>
              <Badge className={`${statusInfo.color} border`}>
                {inspectionOrder.status.replace("_", " ").toUpperCase()}
              </Badge>
            </div>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Progress</span>
                <span className="font-semibold">{statusInfo.step} / 5</span>
              </div>
              <Progress value={progressPercentage} className="h-2" />
            </div>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left Column - Main Content */}
          <div className="lg:col-span-2 space-y-6">
            {/* Timeline */}
            <Card className="animate-fadeInUp" style={{ animationDelay: "0.1s" }}>
              <CardHeader>
                <CardTitle>Order Timeline</CardTitle>
                <CardDescription>Track your inspection and delivery progress</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-6">
                  {[
                    {
                      step: 1,
                      title: "Payment Secured",
                      description: "Your payment has been secured",
                      icon: CheckCircle2,
                      completed: inspectionOrder.status !== "pending",
                    },
                    {
                      step: 2,
                      title: "Under Inspection",
                      description: "Device is being inspected by our technician",
                      icon: FileText,
                      completed: ["under_inspection", "approved", "delivered", "warranty_active"].includes(
                        inspectionOrder.status
                      ),
                    },
                    {
                      step: 3,
                      title: "Inspection Completed",
                      description: inspectionOrder.status === "rejected"
                        ? "Inspection did not meet standards"
                        : "Device passed inspection",
                      icon: inspectionOrder.status === "rejected" ? XCircle : CheckCircle2,
                      completed: ["approved", "delivered", "warranty_active", "rejected"].includes(
                        inspectionOrder.status
                      ),
                    },
                    {
                      step: 4,
                      title: "Delivered",
                      description: "Device has been delivered to you",
                      icon: Truck,
                      completed: ["delivered", "warranty_active"].includes(inspectionOrder.status),
                    },
                    {
                      step: 5,
                      title: "Warranty Active",
                      description: `${inspectionOrder.warranty_duration || 5}-day warranty is now active`,
                      icon: ShieldCheck,
                      completed: inspectionOrder.status === "warranty_active",
                    },
                  ].map((item, index) => (
                    <div key={item.step} className="flex gap-4 animate-fadeIn" style={{ animationDelay: `${0.1 + index * 0.1}s` }}>
                      <div className="flex flex-col items-center">
                        <div
                          className={`w-12 h-12 rounded-full flex items-center justify-center ${
                            item.completed
                              ? "bg-green-500 text-white"
                              : "bg-gray-200 text-gray-400"
                          }`}
                        >
                          <item.icon className="w-6 h-6" />
                        </div>
                        {index < 4 && (
                          <div
                            className={`w-0.5 h-16 mt-2 ${
                              item.completed ? "bg-green-500" : "bg-gray-200"
                            }`}
                          />
                        )}
                      </div>
                      <div className="flex-1 pb-6">
                        <h3 className={`font-semibold ${item.completed ? "text-foreground" : "text-muted-foreground"}`}>
                          {item.title}
                        </h3>
                        <p className="text-sm text-muted-foreground">{item.description}</p>
                        {item.completed && inspectionOrder.status !== "rejected" && (
                          <p className="text-xs text-green-600 mt-1">Completed</p>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Inspection Report - Shown when inspection is complete */}
            {inspectionReport && inspectionOrder.status !== "pending" && (
              <Card className="animate-fadeInUp" style={{ animationDelay: "0.2s" }}>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <FileText className="w-5 h-5 text-blue-600" />
                    Inspection Summary
                  </CardTitle>
                  <CardDescription>Detailed inspection results from our technician</CardDescription>
                </CardHeader>
                <CardContent className="space-y-6">
                  {/* Condition Score */}
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="p-4 rounded-lg border bg-card">
                      <p className="text-sm text-muted-foreground mb-1">Condition Score</p>
                      <p className="text-2xl font-bold text-blue-600">{inspectionReport.condition_score || "N/A"}/10</p>
                    </div>
                    <div className="p-4 rounded-lg border bg-card">
                      <p className="text-sm text-muted-foreground mb-1">Grade</p>
                      <p className="text-2xl font-bold text-indigo-600">{inspectionReport.grade || "N/A"}</p>
                    </div>
                    <div className="p-4 rounded-lg border bg-card">
                      <p className="text-sm text-muted-foreground mb-1">Battery Health</p>
                      <p className="text-2xl font-bold text-green-600">{inspectionReport.battery_health || "N/A"}%</p>
                    </div>
                  </div>

                  {/* Technician Notes */}
                  {inspectionReport.technician_notes && (
                    <div>
                      <h3 className="font-semibold mb-2">Technician Notes</h3>
                      <div className="p-4 rounded-lg border bg-muted/50">
                        <p className="text-sm whitespace-pre-wrap">{inspectionReport.technician_notes}</p>
                      </div>
                    </div>
                  )}

                  {/* Attachments */}
                  {inspectionReport.images && inspectionReport.images.length > 0 && (
                    <div>
                      <h3 className="font-semibold mb-3">Inspection Images</h3>
                      <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                        {inspectionReport.images.map((image, index) => (
                          <div
                            key={index}
                            className="aspect-square rounded-lg overflow-hidden border cursor-pointer hover:opacity-80 transition-opacity"
                            onClick={() => window.open(image.url || image, "_blank")}
                          >
                            <img
                              src={image.url || image}
                              alt={`Inspection image ${index + 1}`}
                              className="w-full h-full object-cover"
                            />
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* Download Report Button */}
                  <Button onClick={downloadReport} className="w-full btn-trust">
                    <Download className="w-4 h-4 mr-2" />
                    Download Full Inspection Report
                  </Button>
                </CardContent>
              </Card>
            )}

            {/* Warranty Information */}
            {inspectionOrder.status === "warranty_active" && (
              <Card className="animate-fadeInUp" style={{ animationDelay: "0.3s" }}>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <ShieldCheck className="w-5 h-5 text-green-600" />
                    Warranty Information
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="flex justify-between items-center">
                    <span className="text-muted-foreground">Warranty Start Date</span>
                    <span className="font-semibold">
                      {new Date(inspectionOrder.warranty_start_date).toLocaleDateString()}
                    </span>
                  </div>
                  <Separator />
                  <div className="flex justify-between items-center">
                    <span className="text-muted-foreground">Warranty End Date</span>
                    <span className="font-semibold">
                      {new Date(inspectionOrder.warranty_end_date).toLocaleDateString()}
                    </span>
                  </div>
                  <Separator />
                  <div className="flex justify-between items-center">
                    <span className="text-muted-foreground">Duration</span>
                    <span className="font-semibold">{inspectionOrder.warranty_duration || 5} days</span>
                  </div>
                  <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                    <p className="text-sm text-blue-900">
                      Your warranty is active. If you experience any issues covered under warranty,
                      please contact our support team immediately.
                    </p>
                  </div>
                </CardContent>
              </Card>
            )}
          </div>

          {/* Right Column - Order Details */}
          <div className="space-y-6">
            <Card className="sticky top-8 animate-fadeInUp" style={{ animationDelay: "0.4s" }}>
              <CardHeader>
                <CardTitle>Order Details</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-3">
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Order Number</span>
                    <span className="font-semibold">{inspectionOrder.order_number || orderIdParam}</span>
                  </div>
                  <Separator />
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Device Price</span>
                    <span className="font-semibold">${inspectionOrder.device_price?.toLocaleString()}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Inspection Fee</span>
                    <span className="font-semibold">${inspectionOrder.inspection_fee?.toFixed(2)}</span>
                  </div>
                  <Separator />
                  <div className="flex justify-between font-semibold">
                    <span>Total Paid</span>
                    <span className="text-blue-600">${inspectionOrder.total_amount?.toFixed(2)}</span>
                  </div>
                  <Separator />
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Order Date</span>
                    <span className="font-semibold">
                      {new Date(inspectionOrder.created_at).toLocaleDateString()}
                    </span>
                  </div>
                  {inspectionOrder.technician_assigned && (
                    <>
                      <Separator />
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Technician</span>
                        <span className="font-semibold">{inspectionOrder.technician_assigned.name}</span>
                      </div>
                    </>
                  )}
                </div>
              </CardContent>
            </Card>

            {/* Contact Support */}
            <Card className="animate-fadeInUp" style={{ animationDelay: "0.5s" }}>
              <CardContent className="pt-6">
                <div className="text-center space-y-3">
                  <AlertCircle className="w-8 h-8 text-blue-600 mx-auto" />
                  <h3 className="font-semibold">Need Help?</h3>
                  <p className="text-sm text-muted-foreground">
                    Contact our support team if you have any questions about your inspection or warranty.
                  </p>
                  <Button
                    variant="outline"
                    className="w-full btn-trust"
                    onClick={() => navigate("/contact-us")}
                  >
                    Contact Support
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default InspectionWarrantyTrackPage;