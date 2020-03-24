--- drivers/apcupsd-ups.h.orig	2015-12-29 13:08:34.000000000 +0100
+++ drivers/apcupsd-ups.h	2020-03-24 17:15:38.000000000 +0100
@@ -133,5 +133,10 @@
 	{ "MINTIMEL", "battery.runtime.low", ST_FLAG_RW, 60, "%.0f", DU_FLAG_NONE, NULL },
 	{ "RETPCT", "battery.charge.restart", ST_FLAG_RW, 1, "%1.1f", DU_FLAG_NONE, NULL },
 	{ "NOMPOWER", "ups.realpower.nominal", 0, 1, "%1.1f", DU_FLAG_INIT, NULL },
+	{ "LOAD_W", "ups.realpower", 0, 1, "%1.1f", DU_FLAG_NONE, NULL },
+	{ "LOADAPNT", "power.percent", ST_FLAG_RW, 1, "%1.1f", DU_FLAG_NONE, NULL },
+	{ "OUTCURNT", "output.current", 0, 1, "%1.2f", DU_FLAG_NONE, NULL },
+	{ "LOAD_VA", "ups.power", 0, 1, "%1.1f", DU_FLAG_NONE, NULL },
+	{ "NOMAPNT", "ups.power.nominal", 0, 1, "%.0f", DU_FLAG_INIT, NULL },
 	{ NULL, NULL, 0, 0, NULL, DU_FLAG_NONE, NULL }
 };
