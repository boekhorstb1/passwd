import React, { useState } from "react";
import Box from "@mui/material/Box";
import TextField from "@mui/material/TextField";
import Stack from "@mui/material/Stack";
import Button from "@mui/material/Button";

export default function Login() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");

  return (
    <div>
      <TextField
        value={username}
        onChange={(e) => setUsername(e.target.value)}
        required
        id="change-password-username"
        label="Username"
      />
      <TextField
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        id="change-password-old-password"
        label="Old Password"
        type="password"
        autoComplete="current-password"
      />
    </div>
  );
}
