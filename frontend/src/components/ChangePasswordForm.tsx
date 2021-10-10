import { SyntheticEvent, useEffect, useState } from "react";
import Box from "@mui/material/Box";
import TextField from "@mui/material/TextField";
import Stack from "@mui/material/Stack";
import Button from "@mui/material/Button";

import UserAuthFields from './UserAuthFields';
import NewPasswordFields from './NewPasswordFields';



export default function ChangePasswordForm() {
  
  interface GlobalThis {
    [key:string]: any; // Add index signature
  }
  const g: GlobalThis = globalThis;

  const [apiMessage, setApiMessage] = useState('');
  
  const onSubmit = (e: SyntheticEvent) => {
    e.preventDefault();
    fetch('/passwd/api/changepw', {'headers': {'Horde-Session-Token': g.hordeSessionToken}}).then((json: any) => {
      return json.json();
    }).then((data: any) => {
      setApiMessage(JSON.stringify(data));
    });
  }
  


  return (
    <>
    <pre>{apiMessage}</pre>
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
            <Button onClick={onSubmit} variant="contained">Change Password</Button>
            <Button variant="outlined">Reset</Button>
          </Stack>
        </div>
      </Box>
    </>
  );
}
