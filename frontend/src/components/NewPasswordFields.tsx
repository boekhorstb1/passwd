import React, { useState, useEffect } from "react";
import Box from "@mui/material/Box";
import TextField from "@mui/material/TextField";
import Stack from "@mui/material/Stack";
import Button from "@mui/material/Button";

export default function UserAuthFields() {
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  return (
    <div>
      <TextField
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        required
        id="change-password-new-password"
        label="New Password"
        type="password"
        autoComplete="current-password"
      />
      <TextField
        value={confirmPassword}
        onChange={(e) => setConfirmPassword(e.target.value)}
        required
        id="change-password-new-password-confirm"
        label="Confirm New Password"
        type="password"
        autoComplete="current-password"
        error={confirmPassword !== password}
        helperText={confirmPassword !== password ? 'Does not match password' : ' '}
      />
    </div>
  );
}
