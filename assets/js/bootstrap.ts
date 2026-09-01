import { createSession } from "@mainmoney/js-core";
import { createCheckout, mountCheckout } from "@mainmoney/js-checkout";

type CheckoutBootConfig = {
  merchantBackendUrl: string;
  clientToken: string;
  pollUrl: string;
  pollHeaders: Record<string, string>;
  locale?: "en" | "fr";
  amount?: string | null;
  currency?: string | null;
  lockAmount?: boolean;
  lockCurrency?: boolean;
  reference?: string;
  targetId?: string;
  logoUrl?: string;
};

declare global {
  interface Window {
    mmAggrCheckouts?: CheckoutBootConfig[];
  }
}

function showBootError(root: HTMLElement, error: unknown): void {
  root.classList.add("mm-checkout");
  const message = error instanceof Error && error.message !== "" ? error.message : "Checkout failed to load.";
  const existing = root.querySelector(".mm-error");
  if (existing !== null) {
    existing.textContent = message;
    return;
  }
  const node = document.createElement("div");
  node.className = "mm-error";
  node.textContent = message;
  root.append(node);
}

async function mountOne(cfg: CheckoutBootConfig): Promise<void> {
  const root = document.getElementById(cfg.targetId ?? "mm-aggr-checkout");
  if (root === null) {
    return;
  }
  try {
    const session = createSession({
      merchantBackendUrl: cfg.merchantBackendUrl,
      clientToken: cfg.clientToken,
      locale: cfg.locale ?? "en",
    });
    const checkout = createCheckout(session, {
      operation: "deposit",
      pollUrl: cfg.pollUrl,
      pollHeaders: cfg.pollHeaders,
      amount: cfg.amount ?? undefined,
      currency: cfg.currency ?? undefined,
      lockCurrency: cfg.lockCurrency === true,
      lockAmount: cfg.lockAmount === true,
      reference: cfg.reference,
    });
    mountCheckout(root, checkout, cfg.logoUrl !== undefined ? { logoUrl: cfg.logoUrl } : {});
    try {
      await checkout.loadCountries();
    } catch (error) {
      showBootError(root, error);
    }
  } catch (error) {
    showBootError(root, error);
  }
}

function boot(): void {
  const configs = window.mmAggrCheckouts ?? [];
  for (const cfg of configs) {
    void mountOne(cfg);
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", boot);
} else {
  boot();
}
