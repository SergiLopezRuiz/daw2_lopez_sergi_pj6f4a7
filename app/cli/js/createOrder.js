function enforceNumericPhone() {
  const phone = document.querySelector('input[name="phone"]');
  if (!phone) return;
  phone.addEventListener('input', () => {
    phone.value = phone.value.replace(/\D+/g, ''); 
  });
}

function handleQtyToggles() {
  document.querySelectorAll('.enableQty').forEach((cb) => {
    const qtyInput = cb.parentElement.querySelector('.qty');
    cb.addEventListener('change', () => {
      qtyInput.disabled = !cb.checked;
      if (cb.checked && (qtyInput.value === '' || qtyInput.value === '0')) qtyInput.value = 1;
      if (!cb.checked) qtyInput.value = 0;
    });
  });

  const btnReset = document.getElementById('btnReset');
  if (btnReset) {
    btnReset.addEventListener('click', () => {
      document.querySelectorAll('.enableQty').forEach((cb) => (cb.checked = false));
      document.querySelectorAll('.qty').forEach((q) => { q.value = 0; q.disabled = true; });
    });
  }
}

function extraClientValidation(form) {
  const name  = form.fullName.value.trim();
  const email = form.email.value.trim();
  const phone = form.phone.value.trim();

  if (!/^[A-Za-zÀ-ÿ\s]{2,60}$/.test(name)) {
    alert('Full name: only letters and spaces (2–60 chars).');
    return false;
  }

  if (!/^[A-Za-z0-9._%+-]+@gmail\.com$/.test(email)) {
    alert('Email must end with @gmail.com.');
    return false;
  }

  if (!/^[0-9]{9,15}$/.test(phone)) {
    alert('Phone must be digits only (9–15).');
    return false;
  }

  const anyQty = Array.from(document.querySelectorAll('.qty'))
    .some((q) => parseInt(q.value, 10) > 0);
  if (!anyQty) {
    alert('Select at least one book (Qty > 0).');
    return false;
  }

  return true;
}

function handleForm() {
  const form = document.getElementById('orderForm');
  const result = document.getElementById('result');
  const resultText = document.getElementById('resultText');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!extraClientValidation(form)) return;

    const fd = new FormData(form);

    try {
      const res = await fetch('../srv/createOrder.php', { method: 'POST', body: fd });
      const data = await res.json();
      result.style.display = 'block';

      if (!res.ok) throw new Error(data?.error || 'Server error');
      resultText.textContent = `Order ${data.orderId} saved successfully. Total with VAT: €${Number(data.totalWithVAT).toFixed(2)}`;
    } catch (err) {
      resultText.textContent = `Error: ${err.message}`;
      result.style.display = 'block';
    }
  });

  document.getElementById('goMenu').addEventListener('click', () => location.href = 'menu.html');
  document.getElementById('goHome').addEventListener('click', () => location.href = 'index.html');
}

window.addEventListener('DOMContentLoaded', () => {
  enforceNumericPhone();
  handleQtyToggles();
  handleForm();
});
