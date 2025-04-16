## Setting Up Tailwind CSS

Install Tailwind CSS

Install tailwindcss and @tailwindcss/cli via npm.
```
npm install tailwindcss @tailwindcss/cli
```

Import Tailwind in your CSS

Add the @import "tailwindcss"; import to your main CSS file.
```
@import "tailwindcss";
```
Start the Tailwind CLI build process

Run the CLI tool to scan your source files for classes and build your CSS.
```
npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch
```
[Click here for more info](https://tailwindcss.com/docs/installation/tailwind-cli)