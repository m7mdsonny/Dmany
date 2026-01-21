import InspectionWarrantyTrackPage from "@/components/PagesComponent/InspectionWarranty/InspectionWarrantyTrackPage";

const InspectionWarrantyTrackRoute = async ({ params, searchParams }) => {
  const { itemId } = await params;
  const orderId = (await searchParams).orderId || "";
  
  return <InspectionWarrantyTrackPage itemId={itemId} orderId={orderId} />;
};

export default InspectionWarrantyTrackRoute;