//Handler for render footer

const Footer = element => {
  window.addEventListener("load", () => {
    element.innerHTML = `
      <footer class="container-lg mt-5 text-center">
        <p class="fw-bold">Developed by 
            <a class="text-decoration-none" target="blank" href="https://github.com/eexzzm/point-of-sale">Group 6</a>
      </footer>
    `;
  });
}

export default Footer;