const dropArea = document.querySelector(".drop_box"),
  button = dropArea.querySelector("button"),
  input = dropArea.querySelector("input");

button.onclick = () => {
  input.click();
};

input.addEventListener("change", function (e) {
  const file = e.target.files[0];
  if (file) {
    const fileName = file.name;
    const form = document.createElement("form");
    form.setAttribute("action", "http://localhost/my_php_project/fileupload.php");
    form.setAttribute("method", "post");
    form.setAttribute("enctype", "multipart/form-data");

    const formContent = `
      <div class="form">
        <h4>${fileName}</h4>
        <input type="email" name="email" placeholder="Enter email to upload file">
        <input type="file" name="file" style="display: none;" value="${fileName}">
        <button type="submit" class="btn">Upload</button>
      </div>
    `;
    form.innerHTML = formContent;

    dropArea.appendChild(form);
  }
});
