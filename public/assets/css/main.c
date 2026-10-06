html {
  font-size: 62.5%;
}

body {
  margin: 0;
  padding: 0;
  overflow: hidden;
  font-size: 1.6rem;
  font-family: "Source Sans 3", sans-serif;
  color: #292929;
}

main {
  min-height: 100vh;
  background-color: #FFC836;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  position: relative;
}
main #logo {
  width: 5rem;
  position: absolute;
  top: 6rem;
}
main #login {
  width: min(90%, 50rem);
  margin: 0 auto;
  background-color: white;
  padding: 1.6rem;
  border-radius: 1.6rem;
}
main #login h1 {
  margin: 2rem 0 5rem;
  font-weight: 700;
}
main #login form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
main #login form .form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin: 0;
}
main #login form .form-group .reset-password {
  font-size: 1rem;
  color: #6E6E6E;
  margin: 0;
  align-self: flex-end;
}
main #login form .connect-button {
  border: none;
  border-radius: 0.8rem;
  padding: 1.2rem 2.5rem;
  font-family: inherit;
  font-weight: 700;
  cursor: pointer;
  border: 0.1rem solid #6E6E6E;
  background-color: white;
  align-self: center;
}
main #login form .connect-button:hover {
  background-color: #FFC836;
}

/*# sourceMappingURL=main.c.map */
