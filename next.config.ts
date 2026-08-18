import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  transpilePackages: [
    "@fullcalendar/core",
    "@fullcalendar/react",
    "@fullcalendar/timegrid",
    "@fullcalendar/daygrid",
    "@fullcalendar/interaction",
  ],
};

export default nextConfig;
