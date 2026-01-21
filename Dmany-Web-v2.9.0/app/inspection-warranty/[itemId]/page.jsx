import InspectionWarrantyPage from "@/components/PagesComponent/InspectionWarranty/InspectionWarrantyPage";

const InspectionWarrantyRoute = async ({ params, searchParams }) => {
  const { itemId } = await params;
  const slug = (await searchParams).slug || "";
  
  return <InspectionWarrantyPage itemId={itemId} slug={slug} />;
};

export default InspectionWarrantyRoute;