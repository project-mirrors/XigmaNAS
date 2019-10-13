--- usb/controller/xhci.c.orig	2019-10-13 13:47:17.667785000 +0200
+++ usb/controller/xhci.c	2019-10-13 14:10:48.000000000 +0200
@@ -599,6 +599,9 @@
 	device_printf(self, "%d bytes context size, %d-bit DMA\n",
 	    sc->sc_ctx_is_64_byte ? 64 : 32, (int)sc->sc_bus.dma_bits);
 
+	/* enable 64Kbyte control endpoint quirk */
+	sc->sc_bus.control_ep_quirk = 1;
+
 	temp = XREAD4(sc, capa, XHCI_HCSPARAMS1);
 
 	/* get number of device slots */
@@ -2001,7 +2004,7 @@
 
 	/* clear TD SIZE to zero, hence this is the last TRB */
 	/* remove chain bit because this is the last data TRB in the chain */
-	td->td_trb[td->ntrb - 1].dwTrb2 &= ~htole32(XHCI_TRB_2_TDSZ_SET(15));
+	td->td_trb[td->ntrb - 1].dwTrb2 &= ~htole32(XHCI_TRB_2_TDSZ_SET(31));
 	td->td_trb[td->ntrb - 1].dwTrb3 &= ~htole32(XHCI_TRB_3_CHAIN_BIT);
 	/* remove CHAIN-BIT from last LINK TRB */
 	td->td_trb[td->ntrb].dwTrb3 &= ~htole32(XHCI_TRB_3_CHAIN_BIT);
