import React, { useState } from "react";
import Box from "@mui/material/Box";
import TextField from "@mui/material/TextField";
import Stack from "@mui/material/Stack";
import Button from "@mui/material/Button";

import UserAuthFields from './UserAuthFields';
import NewPasswordFields from './NewPasswordFields';

export default function ChangePasswordForm() {

  return (
    <>
      <Box
        component="form"
        sx={{
          "& .MuiTextField-root": { m: 1, width: "25ch" },
        }}
        noValidate
        autoComplete="off"
      >
        <UserAuthFields />
        <NewPasswordFields />
        <div>
          <Stack spacing={2} direction="row">
            <Button variant="contained">Change Password</Button>
            <Button variant="outlined">Reset</Button>
          </Stack>
        </div>
      </Box>
    </>
  );
}
